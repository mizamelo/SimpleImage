SimpleImage
==========



SimpleImage, e uma classe criada com o intuito de facilitar a manipulação de imagens no php, necessária as extensões Exif e GD.


Exemplo
-------

	require 'SimpleImage/SimpleImage.php';
	
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
	
	// copia uma imagem para outra, informando a posição da sobreposição
	$simple->merge('../imagem/nova.gif', $percToRight, $percToBottom);
	
	// retorna a altura da imagem
	$simple->getHeight();
	
	// retorna a largura da imagem
	$simple->getWidth();
	
	// retorna o tamanho da imagem
	$simple->getSize();
	
	// retorna o tipo da imagem
	$simple->getType();
	
	// retorna o caminho da imagem
	$simple->getFile();
	
	// escreve na imagem
	* necessário setar a fonte primeiro
	$simple->write($texto, $percToRight, $percToBottom);
	
	// setar a fonte que será usada durante a escrita
	$simple->setFont($caminho);
	
	// seta a cor da fonte
	$simple->setFontColor($hexadecimal);
	
	// seta o tamanho da fonte
	$simple->setFontSize($size);
	
	// salva imagem, caso não seja informado o nome, ele gera um hash e salva
	$simple->save();
	
	
Observações
-----------

Atualmente a classe so trabalha com 3 tipos de imagem, PNG, JPEG e GIF.

	
	
