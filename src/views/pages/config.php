
<style>
	.form-item{
		font-size: 18px;
		color: #224b7a;;
		font-weight:bold;
		margin-top: 5px;
	}
	.form{
		display: flex;
		flex-direction: column;
	}
	.form-item input{
		width: 20rem;
		border: 0;
		font-size: 16px;
		margin: 15px 0 10px 0;
		padding: 10px 5px;
		border-radius: 5px;
		color: #333;
		font-weight: bold;
	}
	hr{
		margin-top: 20px ;
		margin-bottom: 20px;
	}
</style>

<?=$render("header" , ["loggedUser" => $loggedUser])?>
<section class="container main">
    <?=$render("sidebar" , ["activeMenu" =>"home" , "following" => count($loggedUser->following)])?>
 	<div class="feed mt-10">
 		<div class="row">
 			<div class="column mt-5">


 				<?php if(!empty($flash)): ?>
        			<div class="flash">
    					<?=$flash; ?>
        			</div>
    			<?php endif; ?>


 				<form class="form" enctype="multipart/form-data"  action="<?=$base?>/config" method="post">
 					
 					<label class="form-item">
 						Foto:
 						<input type="file" name="avatar" >
 					</label>
 					<label class="form-item">
 						Capa:
 						<input type="file" name="cover" >
 					</label>

 					<hr/>

 					<label class="form-item">
 						Nome Completo:
 						<br/>
 						<input required type="text" name="name" value="<?=$loggedUser->name?>">
 					</label>
 					<br/>

 					<label class="form-item">
 						Data de Nascimento:
 						<br/>
 						<input required id="birthdate" type="text" name="birthdate" value="<?=date( "d/m/Y" ,strtotime($loggedUser->birthDate))?>">
 					</label>

 					<br/>
 					
 					<label class="form-item">
 						E-mail:
 						<br/>
 						<input required type="text" name="email" value="<?=$loggedUser->email?>">
 					</label>
 					<br/>
 					<label class="form-item">
 						Senha Atual:
 						<br/>
 						<input required autocomplete="off" type="password" name="password">
 					</label>
 					<br/>
 					<label class="form-item">
 						Nova Senha:
 						<br/>
 						<input  autocomplete="off" type="password" name="newpassword">
 					</label>
 					<br/>
 					<label class="form-item">
 						Confirme a Nova Senha:
 						<br/>
 						<input  autocomplete="off" type="password" name="newpasswordconfirm">
 					</label>
 					<br/>
 					<label class="form-item">
 						Trabalho:
 						<br/>
 						<input type="text" name="work" value="<?=$loggedUser->work?>">
 					</label>
 					<br/>
 					<label class="form-item">
 						Cidade:
 						<br/>
 						<input type="text" name="city" value="<?=$loggedUser->city?>">
 					</label>
 					<br/>
 					<br/>
 					<br/>
 					<br/>
 					<input class="button" style="margin-top: 20px;" value="Salvar" type="submit">
 				</form>
 			</div>
 		</div>
 	</div>


 </section>
 <script src="https://unpkg.com/imask"></script>
<script>
	IMask(
		document.getElementById("birthdate"),
		{
			mask:"00/00/0000"
		}
	);
</script>
 <?=$render("footer");?>
