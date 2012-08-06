<?php

if ( ! defined('BASE_PATH')) exit('No direct script access allowed');

class controller{
	
	public $content_type;
	
	public $succeed ;
	
	public $error_type;
        
        public $urlpath;
	
	public function __construct($request = ''){
		
		$this->content_type = 'text/html';
		$this->error_type = 0;
		$this->succeed = TRUE;
		
		$request = ltrim($request,'/');
		
		//是否为SAE环境
		if(!IS_SAE || !class_exists('SaeStorage')){//
			$this->error_type = 2;
			$this->succeed = FALSE;
		}
		
		//请求为空
		elseif($request === ''){
			
			//显示欢迎页面
			if(WELCOME_DOC){
				view::show('welcome');
				return ;
			}else{
				$this->error_type = 1;
				$this->succeed = FALSE;
			}
		}
		
		else{
			//匹配文件后缀
			$temp = array();
			if(preg_match('/\.(jpg|jpeg|png|pdf|gif|css|js|zip)$/i', $request,$temp)===1){//暂时先就这几种
				//http://en.wikipedia.org/wiki/Internet_media_type#List_of_common_media_types
				switch($temp[1]){
					case 'jpg':{$this->content_type="image/jpeg";}break;
					case 'gif':{$this->content_type="image/gif";}break;
					case 'png':{$this->content_type="image/png";}break;
					case 'css':{$this->content_type="text/css";}break;
					case 'js':{$this->content_type="text/javascript";}break;
				}
			}
		}
		
		//开始处理
		$this->handle($request);
		
	}
	/**
	 * 获取内容并输出
	 * 如果stroage里面不存在，则从URL里面获取
	 * */
	private function handle($filename){
          //$debugflag = false;
          //$debugmsg = '';
          $this->urlpath = $filename;
          if($filename == 'debug')
            //$debugflag = true;
        
		$content = '';
		if($this->succeed){
                  //$storage = new storage();
                  
                  //$expired = $this->getExpired($filename);//getExpired($filename)
                  //if($debugflag)$debugmsg.='\n<br />expired='.$expired;
                  //  if($debugflag)$debugmsg.='\n<br />max expired='.EXPIREDTIME;
                  //$expiredFlag = false;
                  //if(!($expired === false)){
                  //  if(time() - $expired < EXPIREDTIME){
                  //    $expiredFlag = true;
                  //  }else{
                  //    $expiredFlag = false;
                  //  }
                  //}
                  //if($debugflag)$debugmsg.='\n<br />expiredFlag='.$expiredFlag;
                  
                  $kv = new SaeKV();
        	  $ret = $kv->init();
                  $key = md5($filename);
                  $content = $kv->get($key);
                   if($content === false){
                   	$content = @file_get_contents(BASE_URL.$filename);
                        $kv->set($key, $content);
                   }
                   
                  // if($storage->exists($filename)){
                      //if($debugflag)$debugmsg.='\n<br />read content!';
                  //       $content = $storage->read($filename);
                  // }else{
                  //    //if($debugflag)$debugmsg.='\n<br />download content!';
                  //       $content = @file_get_contents(BASE_URL.$filename);
                  //        $storage->write($filename, $content);
                          
                      //if($expiredFlag === false){
                      //   $this->setExpired($filename);
                      //   if($debugflag)$debugmsg.='\n<br />setExpired';
                      // }
                      //  }
                    
                    
                    if(empty($content)){
                            $this->error_type = 3;
                            $this->succeed = FALSE;
                    }else{
                            //这里应该有更多的检查
                    }
		}
		//显示内容
          //if($debugflag)echo $debugmsg;
          //if(!$debugflag)
                $this->render($content);
	}
        
  /*private function getExpired($filename){
        $key = "fileExpired_".md5($filename);
        $kv = new SaeKV();
        $ret = $kv->init();
        $result = $kv->get($key);

        return $result;
      }
      
      private function setExpired($filename){
        $key = "fileExpired_".md5($filename);
        $kv = new SaeKV();
        $ret = $kv->init();
        $result = $kv->set($key,time());
      }

*/
	/**
	 * 输出结果，包括缓存控制等
	 * */
	private function render($content=''){
		if(!$this->succeed){
			$this->error();
			return ;
		}else{
			header("Expires: " . date("D, j M Y H:i:s GMT", time()+2592000));//缓存一月
			header('Content-type: '.$this->content_type);
			echo $content;
		}
	}
	
	/**
	 * 处理错误
	 * */
	private function error(){
        	header('HTTP/1.1 302 Found');//发出302头部 
		header('Location: '.BASE_URL.$this->urlpath);
		echo "<strong>something seems wrong.</strong>";
	}
	
	
}