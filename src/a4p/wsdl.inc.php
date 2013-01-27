<?php

class wsdl
{
	const BINDING = "http://schemas.xmlsoap.org/soap/http";
	const NS_SOAP = "http://schemas.xmlsoap.org/wsdl/soap/";
	const NS_SOAP_ENC = "http://schemas.xmlsoap.org/soap/encoding/";
	const NS_WSDL = "http://schemas.xmlsoap.org/wsdl/";
	const NS_XML = "http://www.w3.org/2000/xmlns/";
	const NS_XSD = "http://www.w3.org/2001/XMLSchema";

	private $uri;
	private $name;
	private $namespace;
	private $dom;
	private $definitions;
	private $types;
	private $schemas;
	private $portType;
	private $binding;

	private $createdType = array();
	
	public function __construct($classname, $uri)
	{
		$this->name = basename($classname);
		$this->namespace = "urn:" . $this->name . "wsdl";
		$this->uri = $uri;
	}

	public function generate()
	{
		$this->createDocument();

		$class = new ReflectionClass($this->name);
		$methods = $class->getMethods();

		foreach ($methods as $method) {
			if ($method->isPublic() && !$method->isStatic() && !$method->isConstructor()) {
				$this->processMethod($method);
			}
		}

		$this->definitions->appendChild($this->portType);
		$this->definitions->appendChild($this->binding);

		$this->createService();

		return $this->dom->saveXML();
	}

	protected function createService()
	{
		$service = $this->dom->createElementNS(self::NS_WSDL, "service");
		$service->setAttribute("name", $this->name);

		$port = $this->dom->createElementNS(self::NS_WSDL, "port");
		$port->setAttribute("name", $this->name . "Port");
		$port->setAttribute("binding", "tns:" . $this->name . "Binding");

		$address = $this->dom->createElementNS(self::NS_SOAP, "address");
		$address->setAttribute("location", $this->uri);
		$port->appendChild($address);

		$service->appendChild($port);

		$this->definitions->appendChild($service);
	}

	private function processMethod($method)
	{
		$comment = $method->getDocComment();
		
		if (strpos($comment, "@webmethod") == false)
			return;

		$this->addMessage($method);
		$this->addPortTypeOperation($method);
		$this->addBindingOperation($method);
	}

	protected function addPortTypeOperation($method)
	{
		$operation = $this->dom->createElementNS(self::NS_WSDL, "operation");
		$operation->setAttribute("name", $method->name);
		
		$input = $this->dom->createElementNS(self::NS_WSDL, "input");
		$input->setAttribute("message", "tns:" . $method->name . "Request");
		$operation->appendChild($input);

		$output = $this->dom->createElementNS(self::NS_WSDL, "output");
		$output->setAttribute("message", "tns:" . $method->name . "Response");
		$operation->appendChild($output);
	
		$this->portType->appendChild($operation);
	}

	private function addBindingOperation($method)
	{
		$operation = $this->dom->createElementNS(self::NS_WSDL, "operation");
		$operation->setAttribute("name", $method->name);

		$soapOperation = $this->dom->createElementNS(self::NS_SOAP, "operation");
		$soapOperation->setAttribute("soapAction", $this->namespace . "#" . $method->name);
		$soapOperation->setAttribute("style", "rpc");

		$input = $this->dom->createElementNS(self::NS_WSDL, "input");
		$output = $this->dom->createElementNS(self::NS_WSDL, "output");

		$soapBody = $this->dom->createElementNS(self::NS_SOAP, "body");
		$soapBody->setAttribute("use", "encoded");
		$soapBody->setAttribute("encodingStyle", self::NS_SOAP_ENC);

		$input->appendChild($soapBody);
		$output->appendChild(clone $soapBody);

		$operation->appendChild($soapOperation);
		$operation->appendChild($input);
		$operation->appendChild($output);

		$this->binding->appendChild($operation);
	}

	private function getParams($comment)
	{
		$params = array();
		foreach (explode("\n", $comment) as $line) {
			if (preg_match("/^\*\s+@(.[^\s]+)\s+(.[^\s]+)\s+(.[^\s]+)/", trim($line), $match)) {
				if ($match[1] == "param") {
					$param = array();
					$param["type"] = $match[3];
					$param["name"] = $match[2];
					$params[] = $param;
				}
			}
		}
		return $params;
	}

	private function getReturnType($comment)
	{
		$params = array();
		foreach (explode("\n", $comment) as $line) {
			if (preg_match("/^\*\s+@(.[^\s]+)\s+(.[^\s]+)/", trim($line), $match)) {
				if ($match[1] == "return")
					return $match[2];
			}
		}
		return "void";
	}

	private function getVarType($comment)
	{
		$params = array();
		foreach (explode("\n", $comment) as $line) {
			if (preg_match("/^\*\s+@(.[^\s]+)\s+(.[^\s]+)/", trim($line), $match)) {
				if ($match[1] == "var")
					return $match[2];
			}
		}
		return "void";
	}

	private function createArrayType($type)
	{
		$name = $type . "Array";
		if (in_array($name, $this->createdTypes) == true)
			return $name;

		$this->createdTypes[] = $name;

		$complextype = $this->dom->createElementNS(self::NS_XSD, "complexType");
		$complextype->setAttribute("name", $name);

		$sequence = $this->dom->createElementNS(self::NS_XSD, "sequence");
		
		$element = $this->dom->createElementNS(self::NS_XSD, "xsd:element");
		$element->setAttribute("name", $type);
		$element->setAttribute("type", $this->convertType($type));
		$element->setAttribute("minOccurs","0");
		$element->setAttribute("maxOccurs","unbounded");
		
		$sequence->appendChild($element);
		$complextype->appendChild($sequence);
		$this->schemas->appendChild($complextype);

		return $name;
	}

	private function createComplexType($className)
	{
		if (in_array($className, $this->createdTypes) == true)
			return $className;

		$this->createdTypes[] = $className;

		$complextype = $this->dom->createElementNS(self::NS_XSD, "complexType");
		$complextype->setAttribute("name", $className);

		$all = $this->dom->createElementNS(self::NS_XSD, "all");

		$reflection = new ReflectionClass($className);
		$properties = $reflection->getProperties();
		foreach($properties as $property) {
			if ($property->isPublic() && !$property->isStatic()) {
				$comment = $property->getDocComment();
				$type = $this->getVarType($comment);

				$element = $this->dom->createElementNS(self::NS_XSD, "element");
				$element->setAttribute("name", $property->name);
				$element->setAttribute("type", $this->createType($type));

				$all->appendChild($element);
			}
		}
		$complextype->appendChild($all);
		$this->schemas->appendChild($complextype);

		return $name;
	}

	private function createType($type)
	{
		$depth = 0;
		while (substr($type, -2) == "[]") {
			$depth++;
			$type = substr($type, 0, -2);
		}

		$xsd_type = $this->convertType($type);
		if (substr($xsd_type, 0, 2) == "tns")
			$this->createComplexType($type);

		if ($depth > 0) {
			for (; $depth > 0; $depth--)
				$type = $this->createArrayType($type);
			return "tns:" . $type;
		}

		return $xsd_type;
	}

	private function convertType($type)
	{
		switch ($type) {
			case "string":
			case "str":
				return "xsd:string";
				break;
			case "int":
			case "integer":
				return "xsd:int";
				break;
			case "float":
			case "double":
				return "xsd:float";
				break;
			case "boolean":
			case "bool":
				return "xsd:boolean";
				break;
			case "date":
				return "xsd:date";
				break;
			case "time":
				return "xsd:time";
				break;
			case "dateTime":
				return "xsd:dateTime";
				break;
			case "array":
				return "soap-enc:Array";
				break;
			case "object":
				return "xsd:struct";
				break;
			case "mixed":
				return "xsd:anyType";
				break;
			default:
				return "tns:" . $type;
		}
	}

	private function addMessage($method)
	{
		$comment = $method->getDocComment();

		$input = $this->dom->createElementNS(self::NS_WSDL, "message");
		$input->setAttribute("name", $method->name . "Request");
		$this->definitions->appendChild($input);

		$params = $this->getParams($comment);
		foreach ($params as $param) {
			$part = $this->dom->createElementNS(self::NS_WSDL, "part");
			$part->setAttribute("name", $param["name"]);
			$part->setAttribute("type", $this->createType($param["type"]));
			$input->appendChild($part);
		}

		$output = $this->dom->createElementNS(self::NS_WSDL, "message");
		$output->setAttribute("name", $method->name . "Response");
		$this->definitions->appendChild($output);
		
		$returnType = $this->getReturnType($comment);
		if ($returnType != "void") {
			$part = $this->dom->createElementNS(self::NS_WSDL, "part");
			$part->setAttribute("name", $method->name . "Return");
			$part->setAttribute("type", $this->createType($returnType));
			$output->appendChild($part);
		}
	}

	private function createDocument()
	{
		$this->dom = new DOMDocument("1.0", "utf-8");

		$this->definitions = $this->dom->createElementNS(self::NS_WSDL, "wsdl:definitions");
		$this->definitions->setAttribute("name", $this->name);
		$this->definitions->setAttribute("targetNamespace", $this->namespace);
		$this->definitions->setAttributeNS(self::NS_XML, "xmlns", self::NS_WSDL);
		$this->definitions->setAttributeNS(self::NS_XML, "xmlns:tns", $this->namespace);
		$this->definitions->setAttributeNS(self::NS_XML, "xmlns:soap", self::NS_SOAP);
		$this->definitions->setAttributeNS(self::NS_XML, "xmlns:xsd", self::NS_XSD);
		$this->definitions->setAttributeNS(self::NS_XML, "xmlns:wsdl", self::NS_WSDL);
		$this->definitions->setAttributeNS(self::NS_XML, "xmlns:soap-enc", self::NS_SOAP_ENC);
		$this->dom->appendChild($this->definitions);

		$this->types = $this->dom->createElementNS(self::NS_WSDL, "types");
		$this->definitions->appendChild($this->types);

		$this->schemas = $this->dom->createElementNS(self::NS_XSD, "schema");
		$this->schemas->setAttribute("targetNamespace", $this->namespace);
		$this->types->appendChild($this->schemas);

		$this->portType = $this->dom->createElementNS(self::NS_WSDL, "portType");
		$this->portType->setAttribute("name", $this->name . "PortType");

		$this->binding = $this->dom->createElementNS(self::NS_WSDL, "binding");
		$this->binding->setAttribute("name", $this->name . "Binding");
		$this->binding->setAttribute("type", "tns:" . $this->name . "PortType");
		
		$bindingSoap = $this->dom->createElementNS(self::NS_SOAP, "binding");
		$bindingSoap->setAttribute("style", "rpc");
		$bindingSoap->setAttribute("transport", self::BINDING);
		$this->binding->appendChild($bindingSoap);
	}
}
