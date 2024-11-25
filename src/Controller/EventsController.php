<?php
namespace App\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Repository\EventsRepository;
use voku\helper\AntiXSS;

/**
 *
 */
class EventsController
{

	function __construct(private EventsRepository $repository, private AntiXSS $antiXss)
	{

	}

    public function teste(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$response->getBody()->write("Teste executado com sucesso!");
        return $response;
	}

    public function list(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $year = $request->getQueryParams()['year'] ?? date('Y');
        $month = $request->getQueryParams()['month'] ?? date('m');

        if (!checkdate($month, 1, $year)) {
            return $this->respondWithJson($response, [
                'status' => 'error',
                'message' => 'Ano ou mês inválido.'
            ], 400);
        }

        $events = $this->repository->getEventsByMonth($year, $month);

        return $this->respondWithJson($response, [
            'status' => 'success',
            'data' => $events
        ], 200);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody();

        // Validação dos campos
        if (
            empty($data['title']) || 
            empty($data['description']) || 
            empty($data['start_datetime']) || 
            empty($data['end_datetime'])
        ) {
            return $this->respondWithJson($response, [
                'status' => 'error',
                'message' => 'Todos os campos são obrigatórios!'
            ], 400);
        }

        $startDatetime = strtotime($data['start_datetime']);
        $endDatetime = strtotime($data['end_datetime']);
        if ($startDatetime === false || $endDatetime === false) {
            return $this->respondWithJson($response, [
                'status' => 'error',
                'message' => 'Datas inválidas!'
            ], 400);
        }

        if ($startDatetime >= $endDatetime) {
            return $this->respondWithJson($response, [
                'status' => 'error',
                'message' => 'A data de término deve ser posterior à data de início!'
            ], 400);
        }

        $eventId = $this->repository->createEvent([
            'title' => $data['title'],
            'description' => $data['description'],
            'start_datetime' => date('Y-m-d H:i:s', $startDatetime),
            'end_datetime' => date('Y-m-d H:i:s', $endDatetime)
        ]);

        if (!$eventId) {
            return $this->respondWithJson($response, [
                'status' => 'error',
                'message' => 'Erro ao criar o evento!'
            ], 500);
        }

        return $this->respondWithJson($response, [
            'status' => 'success',
            'message' => 'Evento criado com sucesso!',
            'event_id' => $eventId
        ], 201);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $eventId = $args['id'] ?? null;
        $data = $request->getParsedBody();

        if (!$eventId || !is_numeric($eventId)) {
            return $this->respondWithJson($response, [
                'status' => 'error',
                'message' => 'ID do evento inválido ou não fornecido!'
            ], 400);
        }

        // Validação dos campos
        if (
            empty($data['title']) || 
            empty($data['description']) || 
            empty($data['start_datetime']) || 
            empty($data['end_datetime'])
        ) {
            return $this->respondWithJson($response, [
                'status' => 'error',
                'message' => 'Todos os campos são obrigatórios!'
            ], 400);
        }

        // Validação de formato e coerência das datas
        $startDatetime = strtotime($data['start_datetime']);
        $endDatetime = strtotime($data['end_datetime']);
        if ($startDatetime === false || $endDatetime === false) {
            return $this->respondWithJson($response, [
                'status' => 'error',
                'message' => 'Datas inválidas!'
            ], 400);
        }

        if ($startDatetime >= $endDatetime) {
            return $this->respondWithJson($response, [
                'status' => 'error',
                'message' => 'A data de término deve ser posterior à data de início!'
            ], 400);
        }

        $updateSuccess = $this->repository->updateEvent($eventId, [
            'title' => $data['title'],
            'description' => $data['description'],
            'start_datetime' => date('Y-m-d H:i:s', $startDatetime),
            'end_datetime' => date('Y-m-d H:i:s', $endDatetime)
        ]);

        if (!$updateSuccess) {
            return $this->respondWithJson($response, [
                'status' => 'error',
                'message' => 'Erro ao atualizar o evento ou evento não encontrado!'
            ], 404);
        }

        return $this->respondWithJson($response, [
            'status' => 'success',
            'message' => 'Evento atualizado com sucesso!'
        ], 200);
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $eventId = (int) $request->getAttribute('id');

        if ($eventId <= 0) {
            return $this->respondWithJson($response, [
                'status' => 'error',
                'message' => 'ID do evento inválido.'
            ], 400);
        }

        $result = $this->repository->deleteEvent($eventId);

        if (!$result) {
            return $this->respondWithJson($response, [
                'status' => 'error',
                'message' => 'Falha ao excluir o evento.'
            ], 500);
        }

        return $this->respondWithJson($response, [
            'status' => 'success',
            'message' => 'Evento excluído com sucesso.'
        ], 200);
    }

    private function respondWithJson(ResponseInterface $response, array $data, int $statusCode): ResponseInterface
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }
}
