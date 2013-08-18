SimpleImage
===========

SimpleImage, e uma classe criada com o intuito de facilitar a manipulação de imagens no php.


Exemplo
-------

	require 'SimpleImage.php';
	
	$simple = new SimpleImage('../imagem/teste.png');
	
Funções
-------

	// rotaciona a imagem 90º sentido anti-horario
	$simple->rotate90();
	
	// rotaciona a imagem 180º sentido anti-horario
	$simple->rotate180();
	
	// rotaciona a imagem 270º sentido anti-horario
	$simple->rotate270();
	
	// muda o tipo da imagem para gif
	$simple->cloneToGIF();
	
	// muda o tipo da imagem para png
	$simple->cloneToPNG();
	
	// muda o tipo da imagem para jpeg
	$simple->cloneToJPEG();
	
	// corta uma parte da imagem, de acordo com os parâmetros passado
	$simple->crop($width, $height, $percToRight, $percToBottom);
	
	// redimensiona a imagem, para os novos parâmetros informados
	$simple->resize($newWidth, $newHeigth);
	
	// redimensiona a imagem, para os novos parâmetros informados proporcionalmente
	$simple->resizeProportional($newWidth, $newHeigth);
	
	// copia uma imagem para outra, informand a posição da sobreposição
	$simple->merge('../imagem/nova.gif', $percToRight, $percToBottom);
	
	