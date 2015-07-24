<?php
$app->group('/admin', function() use ( $app, $authenticate_admin ) {
	$app->get('/login', function() use( $app ) {
		$app->render('admin/login.phtml', array(
			'page_name' => '登入'
		));
	});
	$app->post('/login', function() use( $app ) {
		$req = $app->request;
        $email = $req->params('email');
        $password = md5( $req->params('password') );
        $admin = Admin::get( $email, $password );
        if( !empty($admin) ) {
            SessionNative::write('ADMIN', $admin);
            // Detection Expired Product
//            $result = Product::get_warranty_expiring();
		$result = array();

            foreach ($result as $key => $value) {
               // Product::change_warranty_status($value['imei_code'], 'expired');
            }
            
            $app->redirect('/admin');
            $app->stop();
        } else {
            $app->flash('error', '帳號或密碼錯誤，請重新輸入。');
            $app->redirect('/admin/login');
        }
	});
    $app->get('/logout', function() use( $app ) {
        SessionNative::delete('ADMIN');
        $app->redirect('/admin');
    });

    
   $app->get('/', $authenticate_admin, function($page = 1) use ( $app ) {
        $admin = SessionNative::read('ADMIN');

		$page = 1;
        $list = Product::get_list($page);
        //array_push($list, $list[0],$list[0],$list[0],$list[0]);

        $app->render('admin/index.phtml', array(
            'page_name' => '產品管理',
            'admin'     => $admin,
            'list'      => $list,
            'page'          => $page
        ));
    });
   
   	$app->get('/bulletin', $authenticate_admin, function($page = 1) use ( $app ) {
   		$admin = SessionNative::read('ADMIN');
   	
   		$page = 1;
   		$list = Product::get_bulletin($page);
   		//array_push($list, $list[0],$list[0],$list[0],$list[0]);
   	
   		$app->render('admin/bulletin.phtml', array(
   				'page_name' => '公告',
   				'admin'     => $admin,
   				'list'      => $list,
   				'page'          => $page
   		));
   	});

    $app->get('/device(/:page)', $authenticate_admin, function($page = 1) use ( $app ) {
        $admin = SessionNative::read('ADMIN');

        if($page < 1)
        	$page = 1;
        
        $list = Product::get_list($page);
        //array_push($list, $list[0],$list[0],$list[0],$list[0]); 

        if(count($list) < 20)
        	$page--;
        
        $app->render('admin/index.phtml', array(
            'page_name' => '產品管理',
            'admin'     => $admin,
            'list'      => $list,
        	'page'		=> $page
        ));
    });
    	$app->get('/serial', $authenticate_admin, function($page = 1) use ( $app ) {
    		$admin = SessionNative::read('ADMIN');
    		$list = Product::get_list_serial($page);
    	
    		$app->render('admin/serial.phtml', array(
    				'page_name' => '產品管理',
    				'admin'     => $admin,
    				'list'      => $list,
    				'page'      => $page
    		));
    	});
    		$app->get('/serial/device(/:page)', $authenticate_admin, function($page = 1) use ( $app ) {
    			$admin = SessionNative::read('ADMIN');
    		
    			if($page < 1)
    				$page = 1;
    		
    			$list = Product::get_list_serial($page);
    			//array_push($list, $list[0],$list[0],$list[0],$list[0]);
    		
    			if(count($list) < 20)
    				$page--;
    		
    			$app->render('admin/serial.phtml', array(
    					'page_name' => '產品管理',
    					'admin'     => $admin,
    					'list'      => $list,
    					'page'		=> $page
    			));
    		});
    			$app->get('/created', $authenticate_admin, function($page = 1) use ( $app ) {
    				$admin = SessionNative::read('ADMIN');
    				$list = Product::get_list_created($page);
    				 
    				$app->render('admin/created.phtml', array(
    						'page_name' => '產品管理',
    						'admin'     => $admin,
    						'list'      => $list,
    						'page'      => $page
    				));
    			});
    			$app->get('/created/device(/:page)', $authenticate_admin, function($page = 1) use ( $app ) {
    				$admin = SessionNative::read('ADMIN');
    			
    				if($page < 1)
    					$page = 1;
    			
    				$list = Product::get_list_created($page);
    				//array_push($list, $list[0],$list[0],$list[0],$list[0]);
    			
    				if(count($list) < 20)
    					$page--;
    			
    				$app->render('admin/created.phtml', array(
    						'page_name' => '產品管理',
    						'admin'     => $admin,
    						'list'      => $list,
    						'page'		=> $page
    				));
    			});
    	$app->get('/q_tag', $authenticate_admin, function($page = 1) use ( $app ) {
    		$admin = SessionNative::read('ADMIN');
    		$list = Product::get_list_qtag($page);
    		
    		$app->render('admin/qtag.phtml', array(
    				'page_name' => '產品管理 - Q Tag',
    				'admin'     => $admin,
    				'list'      => $list,
    				'page'      => $page
    		));
    	});
    		$app->get('/qtag/device(/:page)', $authenticate_admin, function($page = 1) use ( $app ) {
    			$admin = SessionNative::read('ADMIN');
    		
    			if($page < 1)
    				$page = 1;
    			
    			$list = Product::get_list_qtag($page);
    			//array_push($list, $list[0],$list[0],$list[0],$list[0]);
    			
    			if(count($list) < 20)
    				$page--;
    			
    			$app->render('admin/qtag.phtml', array(
    					'page_name' => '產品管理 - Q Tag',
    					'admin'     => $admin,
    					'list'      => $list,
    					'page'		=> $page
    			));
    		});
    		$app->get('/n_tag', $authenticate_admin, function($page = 1) use ( $app ) {
    			$admin = SessionNative::read('ADMIN');
    			$list = Product::get_list_ntag($page);
    			
    			$app->render('admin/ntag.phtml', array(
    					'page_name' => '產品管理 - N Tag',
    					'admin'     => $admin,
    					'list'      => $list,
    					'page'      => $page
    			));
    		});
    			$app->get('/ntag/device(/:page)', $authenticate_admin, function($page = 1) use ( $app ) {
    				$admin = SessionNative::read('ADMIN');
    			
    				if($page < 1)
    					$page = 1;
    				
    				$list = Product::get_list_ntag($page);
    				//array_push($list, $list[0],$list[0],$list[0],$list[0]);
    				
    				if(count($list) < 20)
    					$page--;
    				
    				$app->render('admin/ntag.phtml', array(
    						'page_name' => '產品管理 - N Tag',
    						'admin'     => $admin,
    						'list'      => $list,
    						'page'		=> $page
    				));
    			});
    			$app->get('/b_tag', $authenticate_admin, function($page = 1) use ( $app ) {
    				$admin = SessionNative::read('ADMIN');
    				$list = Product::get_list_btag($page);
    				
    				$app->render('admin/btag.phtml', array(
    						'page_name' => '產品管理 - B Tag',
    						'admin'     => $admin,
    						'list'      => $list,
    						'page'      => $page
    				));
    			});
    				$app->get('/btag/device(/:page)', $authenticate_admin, function($page = 1) use ( $app ) {
    					$admin = SessionNative::read('ADMIN');
    					 
    					if($page < 1)
    						$page = 1;
    					
    					$list = Product::get_list_btag($page);
    					//array_push($list, $list[0],$list[0],$list[0],$list[0]);
    					
    					if(count($list) < 20)
    						$page--;
    					
    					$app->render('admin/btag.phtml', array(
    							'page_name' => '產品管理 - B Tag',
    							'admin'     => $admin,
    							'list'      => $list,
    							'page'		=> $page
    					));
    				});
    				$app->get('/delete(/:serial_number)', $authenticate_admin, function( $serial_number = null ) use ( $app ) {
    					$admin = SessionNative::read('ADMIN');
    					$product = Product::get_by_imei_code( $serial_number );
    					$app->render('admin/delete.phtml', array(
    							'page_name' => '移除',
    							'admin'     => $admin,
    							'p'         => $product
    					));
    				});
    					$app->post('/delete_order', $authenticate_admin, function() use ( $app ) {
    						 
    						$req = $app->request->params();
    						
    						$result = Product::delete($req['serial_number']);
    						
    						if( $result !== false ) {
    							$app->redirect('/admin');
    						} else {
    							//sBuyer::delete( $buyer_id );
    							$app->flash('error', '寫入資料錯誤');
    							$app->redirect('/admin/');
    						}
    					});
    						$app->get('/delete_bulletin(/:id)', $authenticate_admin, function( $id = null ) use ( $app ) {
    							$admin = SessionNative::read('ADMIN');
    							$bulletin = Product::get_bulletin_by_imei_code( $id );
    							$app->render('admin/delete_bulletin.phtml', array(
    									'page_name' => '移除',
    									'admin'     => $admin,
    									'b'         => $bulletin
    							));
    						});
    						$app->post('/remove_bulletin', $authenticate_admin, function() use ( $app ) {
    								
    							$req = $app->request->params();
    						
    							$result = Product::delete_bulletin($req['id']);
    						
    							if( $result !== false ) {
    								$app->redirect('/admin/bulletin');
    							} else {
    								//sBuyer::delete( $buyer_id );
    								$app->flash('error', '寫入資料錯誤');
    								$app->redirect('/admin/bulletin');
    							}
    						});
    						$app->get('/invalid(/:serial_number)', $authenticate_admin, function( $serial_number = null ) use ( $app ) {
    							$admin = SessionNative::read('ADMIN');
    							//var_dump($serial_number);die();
    							$product = Product::get_by_imei_code( $serial_number );
    							$app->render('admin/invalid.phtml', array(
    									'page_name' => '停用',
    									'admin'     => $admin,
    									//'p'         => $product
    									'list'		=> $serial_number
    							));
    						});
    							$app->post('/update_status', $authenticate_admin, function() use ( $app ) {
    									
    								$req = $app->request->params();
    							
    								$result = Product::update_status($req['serial_number']);
    							
    								if( $result !== false ) {
    									$app->redirect('/admin');
    								} else {
    									//sBuyer::delete( $buyer_id );
    									$app->flash('error', '寫入資料錯誤');
    									$app->redirect('/admin/');
    								}
    							});
    								$app->get('/reset(/:serial_number)', $authenticate_admin, function( $serial_number = null ) use ( $app ) {
    									$admin = SessionNative::read('ADMIN');
    									//var_dump($serial_number);die();
    									$product = Product::get_by_imei_code( $serial_number );
    									$app->render('admin/reset.phtml', array(
    											'page_name' => '清空資料',
    											'admin'     => $admin,
    											//'p'         => $product
    											'list'		=> $serial_number
    									));
    								});
    								$app->post('/reset_tag', $authenticate_admin, function() use ( $app ) {
    										
    									$req = $app->request->params();
    										
    									$result = Product::reset_tag($req['serial_number']);
    										
    									if( $result !== false ) {
    										$app->redirect('/admin');
    									} else {
    										//sBuyer::delete( $buyer_id );
    										$app->flash('error', '寫入資料錯誤');
    										$app->redirect('/admin/');
    									}
    								});
    									$app->get('/reopen(/:serial_number)', $authenticate_admin, function( $serial_number = null ) use ( $app ) {
    										$admin = SessionNative::read('ADMIN');
    										//var_dump($serial_number);die();
    										$product = Product::get_by_imei_code( $serial_number );
    										$app->render('admin/reopen.phtml', array(
    												'page_name' => '啟用',
    												'admin'     => $admin,
    												'p'         => $product
    												//'list'		=> $serial_number
    										));
    									});
    									$app->post('/reopen_status', $authenticate_admin, function() use ( $app ) {
    											
    										$req = $app->request->params();
    											
    										$result = Product::reopen_status($req['serial_number']);
    											
    										if( $result !== false ) {
    											$app->redirect('/admin');
    										} else {
    											//sBuyer::delete( $buyer_id );
    											$app->flash('error', '寫入資料錯誤');
    											$app->redirect('/admin/');
    										}
    									});
    $app->get('/add', $authenticate_admin, function() use ( $app ) {
        $admin = SessionNative::read('ADMIN');
        $app->render('admin/add.phtml', array(
            'page_name' => '單筆新增',
            'admin'     => $admin,
        ));
    });
    	$app->get('/add_bulletin', $authenticate_admin, function() use ( $app ) {
    		$admin = SessionNative::read('ADMIN');
    		$app->render('admin/add_bulletin.phtml', array(
    				'page_name' => '新增',
    				'admin'     => $admin,
    		));
    	});
    $app->get('/import', $authenticate_admin, function() use ( $app ) {
    	$admin = SessionNative::read('ADMIN');
    	$app->render('admin/import.phtml', array(
    			'page_name' => '匯入',
    			'admin'     => $admin,
    	));
    });
    	
    	$app->post('/import-csv', $authenticate_admin, function() use ( $app ) {
    		$req = $app->request->params();
	    		$target_dir = getcwd()."/public/uploads/";
    		$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    		//@unlink(getcwd()."/".$target_file);
    			
    		$uploadOk = 1;
    		if ($uploadOk == 0) {
    			echo "Sorry, your file was not uploaded.";
    		} else {
    			move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
    		}
    		
    		$file = $target_file;
    	
    		if (($handle = fopen($file, "r")) !== FALSE) {
    			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    		
    				$data = array(
    						'serial_number'          => $data[0],
    						'status'         => 'new',
    				);
    				Product::add('', $data );
    			}
    		
    			fclose($handle);
    		}

    		$app->flash('import_msg', '匯入序號成功!');
    		$app->redirect('/admin');
    	});
    	
    $app->post('/add_order', $authenticate_admin, function() use ( $app ) {
        $req = $app->request->params();
		
        $product_data = array(
        		'serial_number'     => $req['serial_number'],
        		'membership'		=> $req['membership']
        );
        /*
        $product_data = array(
            'imei_code'     => $req['imei_num'],
            'dealer_name'   => $req['dealer_name'],
            'dealer_num'    => $req['dealer_num'],
            'dealer_shop'   => $req['dealer_shop']
        );
        
        $buyer_data = array(
            'name'          => $req['name'],
            'email'         => $req['email'],
            'phone'         => $req['phone'],
            'gender'        => $req['gender'],
            'zipcode'       => $req['zip_code'],
            'address'       => $req['address']
        );
        // $buyer = Buyer::get_by_email( $buyer_data['email'] );
        // $buyer_id = $buyer === false ? Buyer::add( $buyer_data ) : $buyer['id'];
        $buyer_id = Buyer::add( $buyer_data );
*/
        //if( $buyer_id !== false ) {
            $product = Product::get_by_imei_code( $product_data['serial_number'] );
            if( $product === false ) {
                $result = Product::add( null, $product_data );
                if( $result !== false ) {
                    $app->redirect('/admin');    
                } else {
                    //sBuyer::delete( $buyer_id );
                    $app->flash('error', '寫入資料錯誤');
                    $app->redirect('/admin/add');
                }
            } else {
                $app->flash('error', '此serial number('.$product_data['serial_number'].')已存在!');
                $app->redirect('/admin/add');
            }
        //} else {
           //$app->flash('error', '寫入資料錯誤');
            //$app->redirect('/admin/add');
        //}
    });
    	$app->post('/create_bulletin', $authenticate_admin, function() use ( $app ) {
    		$req = $app->request->params();
    	
    		$product_data = array(
    				'message'     => $req['message']
    		);
    		/*
    		 $product_data = array(
    		 		'imei_code'     => $req['imei_num'],
    		 		'dealer_name'   => $req['dealer_name'],
    		 		'dealer_num'    => $req['dealer_num'],
    		 		'dealer_shop'   => $req['dealer_shop']
    		 );
    	
    		$buyer_data = array(
    				'name'          => $req['name'],
    				'email'         => $req['email'],
    				'phone'         => $req['phone'],
    				'gender'        => $req['gender'],
    				'zipcode'       => $req['zip_code'],
    				'address'       => $req['address']
    		);
    		// $buyer = Buyer::get_by_email( $buyer_data['email'] );
    		// $buyer_id = $buyer === false ? Buyer::add( $buyer_data ) : $buyer['id'];
    		$buyer_id = Buyer::add( $buyer_data );
    		*/
    		//if( $buyer_id !== false ) {
    		//$product = Product::get_by_imei_code( $product_data['serial_number'] );
    		//if( $product === false ) {
    			$result = Product::add_bulletin( null, $product_data );
    			if( $result !== false ) {
    				$app->redirect('/admin/bulletin');
    			} else {
    				//sBuyer::delete( $buyer_id );
    				$app->flash('error', '寫入資料錯誤');
    				$app->redirect('/admin/add_bulletin');
    			}
    		//} else {
    			//$app->flash('error', '此serial number('.$product_data['serial_number'].')已存在!');
    			//$app->redirect('/admin/add');
    		//}
    		//} else {
    		//$app->flash('error', '寫入資料錯誤');
    		//$app->redirect('/admin/add');
    		//}
    	});
    $app->post('/update_order', $authenticate_admin, function() use ( $app ) {
        $req = $app->request->params();
		
        $product_data = array(
        		'membership'   		=> $req['membership']
        );
        
        /*
        $product_data = array(
            'dealer_name'       => $req['dealer_name'],
            'dealer_num'        => $req['dealer_num'],
            'dealer_shop'       => $req['dealer_shop'],
            'imei_num'          => $req['new_imei_num'],
            'service_expired'   => $req['service_expired'],
            'warranty_expired'  => $req['warranty_expired']
        );
        
        $buyer_data = array(
            'name'          => $req['name'],
            'email'         => $req['email'],
            'phone'         => $req['phone'],
            'gender'        => $req['gender'],
            'zipcode'       => $req['zip_code'],
            'address'       => $req['address']
        );
        
        $result = Buyer::update( $req['buyer_id'], $buyer_data );

        if( $result !== false ) {
            $result = Product::update( $req['buyer_id'], $req['imei_num'], $product_data );
        */
        	$result = Product::update( null, $req['imei_num'], $product_data );
            if( $result !== false ) {
                $app->redirect('/admin');    
            } else {
                $app->flash('error', '寫入資料錯誤');
                $app->redirect('/admin/product/'.$req['imei_num']);
            }
        /*
        } else {
            $app->flash('error', '寫入資料錯誤');
            $app->redirect('/admin/product/'.$req['imei_num']);
        }
        */
    });
    	$app->post('/update_bulletin', $authenticate_admin, function() use ( $app ) {
    		$req = $app->request->params();
    	
    		$product_data = array(
    				'message'   		=> $req['message']
    		);
    	
    		$result = Product::update_bulletin( null, $req['id'], $product_data );
    		if( $result !== false ) {
    			$app->redirect('/admin/bulletin');
    		} else {
    			$app->flash('error', '寫入資料錯誤');
    			$app->redirect('/admin/edit_bulletin/'.$req['id']);
    		}
    	});
    		$app->get('/toggle_bulletin(/:id)', $authenticate_admin, function( $id = null ) use ( $app ) {
    			$req = $app->request->params();
    			 
    			$result = Product::toggle_bulletin( $id );
    			
    			$bulletin = Product::get_bulletin_by_imei_code( $id );
    			
    			if( $result !== false ) {
    				if($bulletin['status'] == 'normal')
    					$app->flash('toggle_bulletin_success', '公告巳發佈上架...');
    				else
    					$app->flash('toggle_bulletin_success', '公告巳下架...');
    			} else {
    				$app->flash('toggle_bulletin_error', '寫入資料錯誤');
    			}
    			$app->redirect('/admin/bulletin');
    		});
    			$app->get('/push_bulletin(/:id)', $authenticate_admin, function( $id = null ) use ( $app ) {
    				$req = $app->request->params();
    			
    				//$result = Product::push_bulletin( $id );
    				 
    				//$bulletin = Product::get_bulletin_by_imei_code( $id );
    				 
    				@file_get_contents("http://dev.api.baby-search.com/device/push?id={$id}");
    				
    				$app->flash('push_bulletin_success', '公告巳推播至手機...');
    				/*
    				if( $result !== false ) {
    					if($bulletin['status'] == 'normal')
    						$app->flash('push_bulletin_success', '公告巳發佈上架...');
    					else
    						$app->flash('push_bulletin_success', '公告巳下架...');
    				} else {
    					$app->flash('push_bulletin_error', '寫入資料錯誤');
    				}
    				*/
    				$app->redirect('/admin/bulletin');
    			});
    $app->get('/product(/:imei_code)', $authenticate_admin, function( $imei_code = null ) use ( $app ) {
        $admin = SessionNative::read('ADMIN');
        $product = Product::get_by_imei_code( $imei_code );
        $app->render('admin/product.phtml', array(
            'page_name' => '檢視Tag資料',
            'admin'     => $admin,
            'p'         => $product
        ));
    });
    	$app->get('/edit_bulletin(/:id)', $authenticate_admin, function( $id = null ) use ( $app ) {
    		$admin = SessionNative::read('ADMIN');
    		$bulletin = Product::get_bulletin_by_imei_code( $id );
    		
    		$app->render('admin/edit_bulletin.phtml', array(
    				'page_name' => '編輯',
    				'admin'     => $admin,
    				'b'         => $bulletin
    		));
    	});
    $app->get('/pending', $authenticate_admin, function() use ( $app ) {
        $admin = SessionNative::read('ADMIN');
        $list = Product::get_list_by_pending();

        $app->render('admin/index.phtml', array(
            'page_name' => '待開通設備',
            'admin'     => $admin,
            'list'      => $list
        ));
    });
    $app->get('/expired', $authenticate_admin, function() use ( $app ) {
        $admin = SessionNative::read('ADMIN');
        // Detection Warranty Expired Product
        $result = Product::get_service_expiring();
        foreach ($result as $key => $value) {
            Product::change_service_status($value['imei_code'], 'expired');
        }

        $list_expired = Product::get_service_expired();
        $list_30days = Product::get_list_by_service_30days_expired();

        $app->render('admin/expired.phtml', array(
            'page_name'     => '到期設備',
            'admin'         => $admin,
            'list_expired'  => $list_expired,
            'list_30days'   => $list_30days
        ));
    });
    $app->post('/search', $authenticate_admin, function() use ( $app ) {
        $req = $app->request->post();
        $app->redirect('/admin/product/'.$req['imei_code']);
    });
    $app->get('/test',function() use ( $app ) {
        $app->applyHook('json.dispatch');
        $app->render('json.phtml', array(
            'result' => array(
                'status' => 'ok',
                'msg' => '資料儲存成功'
            )
        ), 200);
    });
    $app->post('/active', $authenticate_admin, function() use ( $app ) {
        $req = $app->request->post();
        foreach ($req['imeis'] as $key => $value) {
            $result = Product::check_pending( $value );
            $result && Product::change_warranty_status( $value, 'active' );
        }
        $app->applyHook('json.dispatch');
        $app->render('json.phtml', array(
            'result' => array(
                'status' => 'ok',
                'msg' => '資料儲存成功'
            )
        ), 200);
    });
});
