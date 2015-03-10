<?php  
	/*ESTA FUNÇÃO GERA UMA SENHA ALEATÓRIA*/
	function gerarNovaSenha($intTamanho = 8, $boolMaiusculas = true, $boolNumeros = true, $boolSimbolos = false){
		// Caracteres de cada tipo
		$lmin = 'abcdefghijklmnopqrstuvwxyz';		
		$lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$num = '1234567890';
		$simb = '!@#$%*-';

		//Variaveis internas
		$retorno = '';
		$caracteres = '';

		//Agrupamos todos os caracteres que poderão ser utilizados
		$caracteres .=$lmin;
		if($boolMaiusculas) $caracteres .= $lmai;
		if($boolNumeros) $caracteres .= $num;
		if($boolSimbolos) $caracteres .= $simb;

		//Calculamos o total de caracteres possíveis
		$len = strlen($caracteres);

		for($n = 1; $n <= $intTamanho; $n++){
			//Criamos um número aleatório de 1 até $len para pegar um dos caracteres
			$rand = mt_rand(1,$len);
			// Concatenamos um dos caracteres na variável $retorno
			$retorno .= $caracteres[$rand-1];
		}
		return $retorno;
	}

	/*ESTA FUNÇÃO RECEBE O E-MAIL DO USUÁRIO E A SENHA GERADA. APÓS ISTO ELA COLOCA A SENHA 
	GERADA NO BANCO DE DADOS*/
	function atualizaSenha($strEmail, $strSenhaGerada){
		include("conexao.php");
		//include("functions.php");
		
		$conexao = bd_conecta();
		
		mysqli_set_charset($conexao,'utf-8');

		$strSenhaGerada = md5($strSenhaGerada);

		$query = "UPDATE usuarios SET senha = '$strSenhaGerada' WHERE  email = '$strEmail'";
		$conexao->query($query);
		
		echo "Atualização bem sucedida!!";

		$conexao->close();
	}

	/*ESTA FUNÇÃO RECEBE O E-MAIL E A SENHA GERADA E ENVIA PARA O EMAIL DO USUÁRIO*/
	function enviarEmail($strEmail, $strSenhaGerada){
		require 'phpmailer/class.phpmailer.php';
		require 'phpmailer/class.smtp.php';
		
		$mail = new PHPMailer();
		$mail->setLanguage('pt');

		/*ESTE TRECHO CONFIGURA O SERVIDOR*/
		$host = "ssl://smtp.gmail.com:465";
		$userName = ''; // email do remetente
		$password = ''; //senha do email remetente
		$port = 587;
		$secure = 'tls';

		/*TRECHO REFERENTE AO REMETENTE*/
		$strFrom = $userName; // ENDEREÇO DE E-MAIL DO REMETENTE
		$strFromName = 'INJUNIOR'; // NOME DO REMETENTE

		/*INICIA A CONEXÃO SMTP*/
		$mail->isSMTP();
		$mail->Host = $host;
		$mail->SMTPAuth = true;
		$mail->Username = $userName;
		$mail->Password = $password;
		$mail->Port = $port;
		$mail->secure = $secure;

		$mail->From = $strFrom;
		$mail->FromName = $strFromName;
		$mail->addReplyTo($strFrom, $strFromName); // ENDEREÇO DE E-MAIL QUE RECEBERÁ UM E-MAIL DE RESPOSTA ENVIADO  PELO USUÁRIO

		$mail->AddAddress($strEmail); // ENDEREÇO NO QUAL A SENHA GERADA SERÁ ENVIADA

		$mail->isHTML(true);
		$mail->CharSet = 'utf-8';

		$mail->Subject = 'Enviando E-mails com PHPMailer'; // ASSUNTO DO E-MAIL
		$mail->Body = '<strong>Nova Senha: </strong>' ."$strSenhaGerada"; // CORPO DO E-MAIL COM HTML
		$mail->AltBody = 'Enviando e-mail em texto plano'; // CORPO DO E-MAIL CASO NÃO TENHA SUPORTE AO HTML

		$send = $mail->Send(); // COMANDO PARA ENVIAR O E-MAIL
		
		/*TESTE PARA VERIFICAR SE O E-MAIL FOI ENVIADO*/
		if($send){
			echo 'E-mail enviado com sucesso!';
		} else {
			echo 'Error:'.$mail->ErrorInfo;
		} 
	}					
?>

<!--FORMULÁRIO DO ESQUECEU SENHA-->
<!DOCTYPE html>
<html>
	<head>
		<title>Esqueceu Senha</title>
	</head>
	<body>
		<form action="" method="POST">
			<label>E-mail:</label>
			<input type="text" name="email" placeholder="digite seu email"/><br><br>
			
			<input type="submit" name="enviar" value="Enviar"/><br><br>
						
			<a href="index.html">Voltar</a>
			<?php  
				if(isset($_POST['enviar'])){
					$strEmail = $_POST['email'];
					$strSenhaGerada = gerarNovaSenha(8); // Definindo uma senha de 8 caracteres
					enviarEmail($strEmail,$strSenhaGerada); // 
					atualizaSenha($strEmail, $strSenhaGerada);
				}
			?>		
		</form>
	</body>
</html>
