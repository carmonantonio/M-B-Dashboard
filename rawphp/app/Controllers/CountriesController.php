<?php

namespace App\Controllers;
use App\Controllers\Controller;
use Respect\Validation\Validator as v; 
use App\Models\Country;

class CountriesController extends Controller{
	
	/**
	* List all users
	* 
	* @return
	*/
	public function index($request, $response,  $args){

        
            $countries = country::all();
            return $this->view->render($response,'countries/index.twig', ['countries'=>$countries]);
        

	}



	/**
	* Display a country
	* 
	* @return
	*/
	public function view($request, $response, $args){
	
	    $country = country::find( $args['id']);
		
		return $this->view->render($response,'countries/view.twig', ['country'=>$country]);
		
	}


	
	/**
	* Create A New country
	* 
	* @return
	*/
	public function add($request, $response,  $args){
	
        if($request->isPost()){
           
            /**
            * validate input before submission
            * @var 
            * 
            */ 
            $validation = $this->validator->validate($request, [
                'name' => v::notEmpty(),	
                'code' => v::notEmpty(),	
            ]);


		//redirect if validation fails
		if($validation->failed()){
			$this->flash->addMessage('error', 'Validation Failed!'); 
		
			return $response->withRedirect($this->router->pathFor('countries/add.twig')); 
		}
		
            $country = country::create([
                'name' => $request->getParam('name'),
                'code' => $request->getParam('code'),
                
            ]);

                $this->flash->addMessage('success', 'Pais añadido satisfactoramente');
                //redirect to eg. countries/view/8 
                return $response->withRedirect($this->router->pathFor('countries.view', ['id'=>$country->id]));
           
        }
		return $this->view->render($response,'countries/add.twig');
		
	}

    
	
	/**
	* Edit country
	* 
	* @return
	*/
	public function edit($request, $response,  $args){
	
              //find the country
            $country = country::find( $args['id']);

			//only admin and the person that created the country can edit or delete it.
			if( $this->auth->user()->role_id > 2  ){
                
			$this->flash->addMessage('error', 'You are not allowed to perform this action!'); 
		
			return $this->view->render($response,'countries/edit.twig', ['country'=>$country]);

			}

        //if form was submitted
        if($request->ispost()){
        
         $validation = $this->validator->validate($request, [
                'name' => v::notEmpty(),	
                'code' => v::notEmpty(),	
            ]);
        //redirect if validation fails
		if($validation->failed()){
			$this->flash->addMessage('error', 'La validacion ha fallado!'); 
		
			return $this->view->render($response,'countries/edit.twig', ['country'=>$country]);
		}
		
            //save Data
            $country =  country::where('id', $args['id'])
                            ->update([
                                'name' => $request->getParam('name'),
                                'code' => $request->getParam('code')
                                ]);
            
            if($country){
                $this->flash->addMessage('success', 'Pais modificado satisfactoriamente');
                //redirect to eg. countries/view/8 
                return $response->withRedirect($this->router->pathFor('countries.view', ['id'=>$args['id']]));
            }
        }
        
	    
		return $this->view->render($response,'countries/edit.twig', ['country'=>$country]);
		
	}


/**
	* Delete a country
	* 
	* @return
	*/
	public function delete($request, $response,  $args){
		$country = country::find( $args['id']);
		
		//only owner and admin can delete
		if ($this->auth->user()->role_id > 2 ){
                
			$this->flash->addMessage('error', 'You are not allowed to perform this action!'); 
		
			return $this->view->render($response,'countries/view.twig', ['country'=>$country]);

			}
			
			
		if($country->delete()){
			$this->flash->addMessage('Exito!', 'Pais eliinado satisfactoriamente');
			return $response->withRedirect($this->router->pathFor('countries.index'));
		}
	}

}