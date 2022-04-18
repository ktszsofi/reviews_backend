<?php
    class Review{
        private $table = 'reviews';
        private $db;
        private $requestMethod;

        public function __construct($db, $requestMethod){
            $this->db = $db;
            $this->requestMethod = $requestMethod;
        }

        public function processRequest()
        {
            switch ($this->requestMethod) {
            case 'GET':
                $response = $this->getReviews();
                break;
            case 'POST':
                $response = $this->postReview();
                break;
            default:
                $response = $this->notFoundResponse();
                break;
            }

            header($response['status_code_header']);
            if ($response['body']) {
                echo $response['body'];
            }
        }

        public function getReviews(){
            $query = 'SELECT * FROM ' . $this->table . ' ORDER BY created_at DESC';

            try {
                $stm = $this->db->query($query);
                $result = $stm->fetchAll(\PDO::FETCH_ASSOC);
              } catch (\PDOException $e) {
                exit($e->getMessage());
              }
          
              $response['status_code_header'] = 'HTTP/1.1 200 OK';
              $response['body'] = json_encode($result);
              return $response;
            }


            public function getReview($id){
                $query = 'SELECT * FROM ' . $this->table . ' WHERE id=' . $id;
    
                try {
                    $stm = $this->db->query($query);
                    $result = $stm->fetchAll(\PDO::FETCH_ASSOC);
                  } catch (\PDOException $e) {
                    exit($e->getMessage());
                  }

                  return json_encode($result);
                }

            private function postReview()
            {
              $input = (array) json_decode(file_get_contents('php://input'), TRUE);
              if (! $this->validateReview($input)) {
                return $this->unprocessableEntityResponse();
              }
          
              $query = '
                INSERT INTO ' . $this->table . ' (name, score, comment) VALUES (:name, :score, :comment);';
          
              try {
                $statement = $this->db->prepare($query);
                $statement->execute(array(
                  'name' => $input['name'],
                  'score'  => $input['score'],
                  'comment' => isset($input['comment']) ? $input['comment'] : null,
                ));
                $insert_id = $this->db->lastInsertId();

              } catch (\PDOException $e) {
                exit($e->getMessage());
              }
          
              $response['status_code_header'] = 'HTTP/1.1 201 Created';
              $response['body'] = $this->getReview($insert_id);
              return $response;
            }

            private function validateReview($input)
            {
              if (! isset($input['name'])) {
                return false;
              }
              if (! isset($input['score'])) {
                return false;
              }
          
              return true;
            }

            private function unprocessableEntityResponse()
            {
              $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
              $response['body'] = json_encode([
                'error' => 'Invalid input'
              ]);
              return $response;
            }

            private function notFoundResponse()
            {
              $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
              $response['body'] = null;
              return $response;
            }
        }
    