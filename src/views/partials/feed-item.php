<div class="box feed-item" data-id="<?=$data->id?>">
    <div class="box-body">
        <div class="feed-item-head row mt-20 m-width-20">
            <div class="feed-item-head-photo">
                <a href="<?=$base;?>/perfil/<?=$data->user->id?>"><img src="<?=$base;?>/media/avatars/<?=$data->user->avatar?>" /></a>
            </div>
            <div class="feed-item-head-info">
                <a href="<?=$base;?>/perfil/<?=$data->user->id?>"><span class="fidi-name"><?=$data->user->name;?></span></a>
                <span class="fidi-action">
                    <?php 
                        if($data->type == "text"){echo "Fez um post.";}
                        if($data->type == "photo"){echo "Postou uma foto.";}
                    ?>
                    
                </span>
                <br/>
                <span class="fidi-date"><?=date( "H:i d/m/Y" ,strtotime($data->created_at))?></span>
            </div>
            <?php if($user->id == $data->user->id):?>
            <div class="feed-item-head-btn">
                <img src="<?=$base;?>/assets/images/more.png" />
                <div class="feed-item-more-window">
                    <a href="<?=$base;?>/post/<?=$data->id?>/excluir">Excluir Post</a>
                </div>
            </div>
            <?php endif;?>
        </div>
        <div class="feed-item-body mt-10 m-width-20">
            

            <?php if($data->type == "text"):?>
                <?=nl2br($data->body);?>
            <?php endif; ?>


            <?php if($data->type == "photo"):?>
                <img src="<?=$base;?>/media/uploads/<?=$data->body;?>"  alt="IMG do Post">
            <?php endif; ?>

            
        </div>
        <div class="feed-item-buttons row mt-20 m-width-20">
            <div class="like-btn <?php if($data->liked) echo "on";?>"><?=$data->likeCount?></div>
            <div class="msg-btn"><?=count($data->comments)?></div>
        </div>
        <div class="feed-item-comments">
            
            <div class="feed-item-comments-area">
                <?php foreach($data->comments as $item):?>                    
                    <div class="fic-item row m-height-10 m-width-20">
                        <div class="fic-item-photo">
                            <a href="<?=$base;?>/perfil/<?=$item["user"]["id"]?>"><img src="<?=$base;?>/media/avatars/<?=$item["user"]["avatar"]?>" /></a>
                        </div>
                        <div class="fic-item-info">
                                <a href="<?=$base;?>/perfil/<?=$item["user"]["id"]?>"><?=$item["user"]["name"]?></a>
                                    <?=$item["body"]?>
                        </div>
                    </div>
                <?php endforeach;?>                    
            </div>

        

            <div class="fic-answer row m-height-10 m-width-20">
                <div class="fic-item-photo">
                    <a href=""><img src="<?=$base;?>/media/avatars/<?=$user->avatar?>" /></a>
                </div>
                <input type="text" class="fic-item-field" placeholder="Escreva um comentário" />
            </div>



        </div>
    </div>
</div>