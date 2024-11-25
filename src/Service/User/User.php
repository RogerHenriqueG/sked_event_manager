<?php
namespace App\Service\User;
use PDOException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Helper\Helper;
use Slim\Psr7\Response;
use App\Service\Sql\Sql;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


/**
 *
 */
class User
{
	public static function login(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$inputData = $request->getParsedBody();
		$email = isset($inputData['user']) ? filter_var($inputData['user'], FILTER_SANITIZE_EMAIL) : '';
		$password = $inputData['password'] ?? '';

		// Validação dos campos
		if (empty($email) || empty($password)) {
			return self::respondWithJson($response, [
				'cod' => 0,
				'message' => 'Preencha os campos corretamente!'
			], 400, 'Preencha os campos corretamente!');
		}

		try {
			$sql = new Sql();
			$stmt = $sql->prepare(
				'SELECT 
					u.id,
					u.name,
					u.email,
					u.password,
					u.created_at,
					u.updated_at,
					CASE
						WHEN u.deleted_at IS NULL THEN "active"
						ELSE "deleted"
					END AS status
				FROM users u
				WHERE u.email = :email'
			);

			$stmt->execute([':email' => $email]);
			$user = $stmt->fetch($sql::FETCH_ASSOC);

			// Verifica se o usuário existe
			if (!$user) {
				return self::respondWithJson($response, [
					'cod' => 0,
					'message' => 'Usuário não encontrado ou senha inválida!'
				], 401, 'Usuário não encontrado ou senha inválida!');
			}

			// Valida senha
			if (!password_verify($password, $user['password'])) {
				return self::respondWithJson($response, [
					'cod' => 0,
					'message' => 'Senha inválida!'
				], 401, 'Senha inválida!');
			}

			// Registra sessão do usuário
			$_SESSION['user'] = [
				'id' => $user['id'],
				'name' => $user['name'],
				'email' => $user['email']
			];

			return self::respondWithJson($response, [
				'cod' => 1,
				'message' => '/home'
			], 200, 'Encaminhar usuário para dispatcher!');

		} catch (PDOException $e) {
			error_log('Erro no login: ' . $e->getMessage());

			return self::respondWithJson($response, [
				'cod' => 0,
				'message' => 'Erro interno no servidor. Tente novamente mais tarde.'
			], 500, 'Erro interno no servidor.');
		}
	}

	public static function sair(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		@session_start();
		session_destroy();
		$response = new Response();
        return $response->withHeader('Location','/')->withStatus(302);
	}

	/**
	 * Método utilitário para responder com JSON.
	 */
	private static function respondWithJson(ResponseInterface $response, array $data, int $statusCode, string $headerMessage): ResponseInterface
	{
		$response->getBody()->write(json_encode($data));
		return $response
			->withStatus($statusCode)
			->withHeader('Content-Type', 'application/json')
			->withHeader('X-Login', $headerMessage);
	}
}