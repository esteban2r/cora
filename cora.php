<?php

	/**
	 * Leer archivos y cargarlos
	 */
	$decorador = new DOMDocument();
	if(!$decorador->Load('decorator.xml')){
		echo 'No existe el archivo decorator.xml';
		return;
	}

	$source = new DOMDocument;

	//Si el archivo source.xml existe, utilizarlo	
	if(!file_exists('source.xml')){

		//Si el archivo Intro.xml existe, utilizarlo	
		if(!file_exists('Intro.xhtml')){

			//Si no existe ninguno de los dos anteriores archivos, mostrar mensaje de error y no continuar
			echo 'No existe el archivo source.xml ni el archivo Intro.xhtml ';
			return;
		}else{
			echo 'Archivo Intro.xhtml encontrado' . '<br><br>';
			$source->loadHTMLFile('Intro.xhtml');
			$typeSourceFile = 'xhtml';
		}
	}else{
		echo 'Archivo source.xml encontrado' . '<br><br>';
		$source->Load('source.xml');
		$typeSourceFile = 'xml';
	}

	$xpath = new DOMXPath($source);

	$ruta = '';
	
	foreach ($decorador->getElementsByTagName('*') as $element) {

		if ($element->localName == 'decorator') {
			$ruta = $element->getAttribute('ref');
		}

		if ($element->localName == 'append') {

			foreach ($element->childNodes as $childElement) {

				//Evita los tags con espacios vacios
				if($childElement->nodeName != '#text'){

					//Crea un nuevo elemento con las propiedades del hijo
					$newElement = $source->createElement($childElement->tagName, $childElement->nodeValue);

					foreach ($childElement->attributes as $attr) {
						//Crea nuevo atributo y lo adiciona al nuevo tag
						$newAttribute = $source->createAttribute($attr->name);
						$newAttribute->value = $attr->value;
						$newElement->appendChild($newAttribute);

						//Crea atributo xmlns y adiciona al nuevo tag
						/*						
						$newAttribute = $source->createAttribute('xmlns');
						$newAttribute->value = $attr->namespaceURI;
						$newElement->appendChild($newAttribute);
						*/
					}


					//Con el xpath, se obtiene el elemento de acuerdo a la ruta especificada ($ruta)
					$tagToInsert = $xpath->query('/'.$ruta)->item(0);

					//Inserta en el tag el nuevo elemento
					append($tagToInsert, $newElement);

				}

			}
		}

		if($element->localName == 'insert'){
			
			foreach ($element->childNodes as $childElement) {

				//Evita los tags con espacios vacios
				if($childElement->nodeName != '#text'){

					//Crea un nuevo elemento con las propiedades del hijo
					$newElement = $source->createElement($childElement->tagName, $childElement->nodeValue);

					//Con el xpath, se obtiene el elemento de acuerdo a la ruta especificada ($ruta)
					$tagWhereInsert = $xpath->query($ruta)->item(0);

					//Inserta en el tag el nuevo elemento
					insertBefore($newElement, $tagWhereInsert);

				}

			}
		}
		if($element->localName == 'replace'){
			
			foreach ($element->childNodes as $childElement) {

				//Evita los tags con espacios vacios
				if($childElement->nodeName != '#text'){

					//Crea un nuevo elemento con las propiedades del hijo
					$newElement = $source->createElement($childElement->tagName, $childElement->nodeValue);

					//Con el xpath, se obtiene el elemento de acuerdo a la ruta especificada ($ruta)
					$tagWhereReplace = $xpath->query($ruta)->item(0);

					//Inserta en el tag el nuevo elemento
					replace($newElement, $tagWhereReplace);

				}

			}
		}

	}

	/**
	 * Exportar a xml result
	 */
	if($typeSourceFile == 'xml'){
		$source->save("resultado.xml");
	}else{
		$source->save("resultado.xhtml");
	}
	echo "Success";

	/*
	 *Funciones principales para el cora
	 */
	function append($parent, $child){
		$parent->appendChild($child);
	}

	function insertBefore($newNode, $node){
		$node->parentNode->insertbefore($newNode, $node);
	}

	function replace($newNode, $nodeToReplace){
		$nodeToReplace->parentNode->replaceChild($newNode, $nodeToReplace);
	}

	/*
	 * Debugging
	 */
	function pre(){
		$values = func_get_args();
		foreach($values as $val){
			echo "<pre>";
			print_r($val);
			echo "</pre>";
		}
	}
?>