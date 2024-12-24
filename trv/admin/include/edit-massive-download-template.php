<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/verifySession.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/admin/include/prods-export/SimpleXLSXGen.php";
	
	use Shuchkin\SimpleXLSXGen;
	
	$existeError = false;
	
	if(isset($_POST["downloadTemplateToken"]) && $_POST["downloadTemplateToken"] == "exz27"){
	$data = [
	['<center><b>INSTRUCCIONES EDICIÓN MASIVA DE PRODUCTOS</b></center>'],
	[''],
	['En este archivo encontrará una planilla para editar masivamente los productos de su catálogo en su Sistema POS.'],
	['<b>Por favor lea detalladamente estas instrucciones para evitar conflictos al momento de importar los productos.</b>'],
	[''],
	['Al ingresar a la hoja "Lista de productos" encontrará los campos correspondientes para editar los artículos. Se compone de 7 columnas:'],
	[''],
	['', '--->', '--->', '--->', '--->', ''],
	['<center><b>ID (NO MODIFICAR)</b></center>', '<center><b>Nombre del producto</b></center>', '<center><b>Precio de venta</b></center>', '<center><b>Precio de compra</b></center>', '<center><b>Categoría</b></center>', '<center><b>Código de barras</b></center>'],
	['<b>Por favor NO MODIFIQUE esta columna.</b>', 'Escriba el nombre del artículo.', 'Escriba el precio de venta con impuestos incluidos.', 'Escriba el precio de compra del artículo.', 'Ingrese el código de la categoría correspondiente.', 'Código de barras del producto.'],
	['<b>Este es el identificador de cada uno de los productos.</b>', '', 'NO incluya puntos ni comas.', 'NO incluya puntos ni comas.', '<b>Ingrese a su Sistema POS para ver los códigos disponibles.</b>', 'Preferiblemente sin espacios ni caracteres especiales.'],
	[''],
	[''],
	['<center><b>RECOMENDACIONES</b></center>'],
	[''],
	['Por favor no modifique los encabezados, de lo contrario se omitirá la actualización de los productos.'],
	[''],
	['No deje espacios en blanco, todos los campos son obligatorios. Si el precio de venta es 0, escriba ese valor en el recuadro.'],
	[''],
	['Revise la categoría de los productos a agregar, si esta no existe, el/los artículo/s no se modificarán.']
	];
	
	$data2 = [
	['<center><b>ID (NO MODIFICAR)</b></center>', '<center><b>Nombre del producto</b></center>', '<center><b>Precio de venta</b></center>', '<center><b>Precio de compra</b></center>', '<center><b>Categoría</b></center>', '<center><b>Código de barras</b></center>']
	];
	
	$sql = "SELECT * FROM trvsol_products";
	$result = $conn->query($sql);
	if($result->num_rows > 0){
	while($row = $result->fetch_assoc()){
	array_push($data2, ["<left>" . $row["id"] . "</left>", "<left>" . $row["nombre"] . "</left>", "<right>" . $row["precio"] . "</right>", "<right>" . $row["purchasePrice"] . "</right>", "<right>" . $row["categoryID"] . "</right>", "<right>" . $row["barcode"] . "</right>"]);
	}}

	$xlsx = new SimpleXLSXGen();
	$xlsx->addSheet($data, 'Instrucciones' );
	$xlsx->addSheet($data2, 'Lista de productos');
	$xlsx->setDefaultFont('Arial');
	$xlsx->setDefaultFontSize(12);
	$xlsx->downloadAs('edicion-masiva-productos-' . date("Y-m-d-h-ia") . '.xlsx');
	}else{
	$existeError = true;
	}
	
	if($existeError == true){ echo "There was an error al generar la plantilla<br><a href= '/trv/home.php'>Volver al inicio</a>"; }
?>