<?php

return array
	(
		'name' => 'stfile',
		'version' => '1.0.0',
		'vendor' => 995002,
		'description' => 'Позволяет добавлять файлы к товарам',
		'img' => 'img/files.svg',
		//'shop_settings' => true,
		'handlers' => array
						(
							
							'backend_menu' => 'backendMenu',
							'backend_product' => 'backendProduct',
							'backend_products' => 'backendProducts',
							'frontend_product' => 'frontendProduct',
							

							/*
							'backend_order' => 'backendOrder',
							'backend_orders' => 'backendOrders',
							'order_action.edit' => 'orderEdit',
							'orders_collection' => 'ordersCollection',
							'stmgr_backend_menu' => 'stmgrBackendMenu',
							*/
						),
	);