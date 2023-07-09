<?=$render("header" , ["loggedUser" => $loggedUser])?>            
            
<section class="container main">
 	<?=$render("sidebar" , ["activeMenu" => "photos" , "following" => count($user->following)])?>
       <section class="feed mt-10">


       	<section class="feed">

            <?=$render("perfil-header" ,["user" => $user , "loggedUser" => $loggedUser , "isFollowing" => $following ])?>
            
            
                
            <div class="column">
                    
                    
            	<div class="column">
                    
                    <div class="box">
                        <div class="box-body">

                            <div class="full-user-photos">

                                <?php if(count($user->photos) == 0):?>
                                	Este Usuário não possui Fotos.
                                <?php endif; ?> 
                                <?php if(count($user->photos) > 0):?>
                                    <?php foreach($user->photos as $photo):?>
                                    	<div class="user-photo-item">
                                        	<a href="#modal-<?=$photo->id?>" rel="modal:open">
                                            	<img src="<?=$base;?>/media/uploads/<?=$photo->body?>" />
                                        	</a>
                                        	<div id="modal-<?=$photo->id?>" style="display:none">
                                            	<img src="<?=$base;?>/media/uploads/<?=$photo->body?>" />
                                        	</div>
                                    	</div>
                                    <?php endforeach;?>
                                <?php endif;?>
    
                                

                            </div>
                            

                        </div>
                    </div>

                </div>


            </div>

        </section>


 </section>
 <?=$render("footer");?>