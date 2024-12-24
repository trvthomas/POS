<?php
//File with all of the databases used in the POS System

//Create tables
//trvsol_admin_images
$dbOptimize_trvsol_admin_images = "CREATE TABLE IF NOT EXISTS trvsol_admin_images(
	id int(11) NOT NULL AUTO_INCREMENT,
	url text NOT NULL,
	name text NOT NULL,
	PRIMARY KEY (id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

//trvsol_categories
$dbOptimize_trvsol_categories = "CREATE TABLE IF NOT EXISTS trvsol_categories(
	id int(11) NOT NULL AUTO_INCREMENT,
	nombre text NOT NULL,
	color varchar(8) NOT NULL DEFAULT '#3e4095',
	color_txt varchar(8) NOT NULL DEFAULT '#fff',
	emoji varchar(10) NOT NULL DEFAULT '🔲',
	PRIMARY KEY (id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

//trvsol_invoices
$dbOptimize_trvsol_invoices = "CREATE TABLE IF NOT EXISTS trvsol_invoices(
	id int(11) NOT NULL AUTO_INCREMENT,
	numero text NOT NULL,
	mes int(11) NOT NULL,
	year int(11) NOT NULL,
	fecha date NOT NULL,
	fechaComplete text NOT NULL,
	vendedor text NOT NULL,
	idSeller int(11) NOT NULL,
	productos text NOT NULL,
	productos_cambio text NOT NULL,
	productosArray text NOT NULL,
	productosArray_autoPrint text NOT NULL,
	formaPago varchar(20) NOT NULL,
	subtotal int(11) NOT NULL,
	descuentos int(11) NOT NULL,
	multipagoEfectivo int(11) NOT NULL,
	multipagoTarjeta int(11) NOT NULL,
	multipagoOtro int(11) NOT NULL,
	recibido text NOT NULL,
	cambio int(11) NOT NULL,
	notas varchar(150) NOT NULL,
	cancelada int(11) NOT NULL,
	canceladaPor text NOT NULL,
	PRIMARY KEY (id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

//trvsol_products
$dbOptimize_trvsol_products = "CREATE TABLE IF NOT EXISTS trvsol_products(
	id int(11) NOT NULL AUTO_INCREMENT,
	nombre text NOT NULL,
	precio int(11) NOT NULL,
	variable_price int(11) NOT NULL,
	array_prices text NOT NULL DEFAULT '[]',
	imagen text NOT NULL,
	barcode text NOT NULL,
	categoryID int(11) NOT NULL,
	purchasePrice int(11) NOT NULL,
	stock int(11) NOT NULL,
	tags text NOT NULL,
	service int(1) NOT NULL,
	control_inventory int(1) NOT NULL DEFAULT '1',
	activo int(1) NOT NULL DEFAULT '1',
	ventasMensuales int(11) NOT NULL,
	display_order int(11) NOT NULL,
	PRIMARY KEY (id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

//trvsol_products_stats
$dbOptimize_trvsol_products_stats = "CREATE TABLE IF NOT EXISTS trvsol_products_stats(
	id int(11) NOT NULL AUTO_INCREMENT,
	year int(4) NOT NULL,
	productId int(11) NOT NULL,
	estadisticas text NOT NULL,
	PRIMARY KEY (id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

//trvsol_stats
$dbOptimize_trvsol_stats = "CREATE TABLE IF NOT EXISTS trvsol_stats(
	id int(11) NOT NULL AUTO_INCREMENT,
	mes int(2) NOT NULL,
	year int(4) NOT NULL,
	estadisticas text NOT NULL,
	reportSent int(1) NOT NULL,
	PRIMARY KEY (id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

//trvsol_users
$dbOptimize_trvsol_users = "CREATE TABLE IF NOT EXISTS trvsol_users(
	id int(11) NOT NULL AUTO_INCREMENT,
	username text NOT NULL,
	password text NOT NULL,
	inventory int(1) NOT NULL,
	admin int(1) NOT NULL,
	securityCode int(4) NOT NULL,
	PRIMARY KEY (id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

//trvsol_users_stats
$dbOptimize_trvsol_users_stats = "CREATE TABLE IF NOT EXISTS trvsol_users_stats(
	id int(11) NOT NULL AUTO_INCREMENT,
	year int(12) NOT NULL,
	userId int(11) NOT NULL,
	estadisticas text NOT NULL,
	PRIMARY KEY (id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

//trvsol_vouchers
$dbOptimize_trvsol_vouchers = "CREATE TABLE IF NOT EXISTS trvsol_vouchers(
	id int(11) NOT NULL AUTO_INCREMENT,
	code varchar(80) NOT NULL,
	totalAvailable int(11) NOT NULL,
	minimumQuantity int(11) NOT NULL,
	value int(11) NOT NULL,
	paymentMethods text NOT NULL,
	expiration date NOT NULL,
	color varchar(8) NOT NULL,
	color_txt varchar(8) NOT NULL,
	PRIMARY KEY (id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

//trvsol_vouchers_stats
$dbOptimize_trvsol_vouchers_stats = "CREATE TABLE IF NOT EXISTS trvsol_vouchers_stats(
	id int(11) NOT NULL AUTO_INCREMENT,
	year int(4) NOT NULL,
	voucherId int(11) NOT NULL,
	estadisticas text NOT NULL,
	PRIMARY KEY (id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

//trvsol_inventory
$dbOptimize_trvsol_inventory = "CREATE TABLE IF NOT EXISTS trvsol_inventory(
	id int(11) NOT NULL AUTO_INCREMENT,
	date date NOT NULL,
	hour varchar(6) NOT NULL,
	type varchar(10) NOT NULL,
	reason varchar(50) NOT NULL,
	notes text NOT NULL,
	productsArray text NOT NULL,
	productsArrayComplete text NOT NULL,
	productsAdded int(11) NOT NULL,
	PRIMARY KEY (id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

//trvsol_configuration
$dbOptimize_trvsol_configuration = "CREATE TABLE IF NOT EXISTS trvsol_configuration(
	id int(11) NOT NULL AUTO_INCREMENT,
	configName text NOT NULL,
	value text NOT NULL,
	PRIMARY KEY (id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
?>