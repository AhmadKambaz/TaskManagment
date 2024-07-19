<?php


namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Task;
use CodeIgniter\RESTful\ResourceController;

class TaskController extends BaseController
{
    //protected $model = new Task();
    protected $format    = 'json';

    // List all tasks with pagination
    public function index()
    {
        $model = new Task();
        $currentPage = $this->request->getVar('page') ?? 1;

        $perPage = 10;
        $tasks = $model->paginate($perPage, 'default', $currentPage);
        $pager = $model->pager;
        return $this->response->setJSON([
            'status' => 200,
            'error' => false,
            'data' => $tasks,
            'pagination' => [
                'total' => $pager->getTotal(),
                'perPage' => $perPage,
                'currentPage' => $pager->getCurrentPage(),
                'lastPage' => $pager->getLastPage(),
            ],
        ])->setStatusCode(200);
    }

    // Show a single task
    public function show($id = null)
    {
        $model = new Task();
        $task = $model->find($id);

        if (!$task) {
            return $this->response->setJSON([
                'status' => 404,
                'error' => false,
                'meassges' => "Task with id $id not found.",
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'status' => 200,
            'error' => false,
            'data' => $task,
        ])->setStatusCode(200);
    }

    // Create a new task
    public function create()
    {
        $model = new Task();
        $data = $this->request->getJSON(true);
        $rules = [
            'title' => 'required',
            'description' => 'permit_empty',
            'status' => 'required|in_list[pending,in-progress,completed]',
            'due_date' => 'permit_empty|valid_date'
        ];
        if ($this->validate($rules)) {
            if ($model->save($data)) {
                $taskId = $model->insertID();
                $task = $model->find($taskId);
                return $this->response->setJSON([
                    'status' => 201,
                    'error' => false,
                    'data' => $task,
                ])->setStatusCode(201);
            }
            return $this->response->setJSON([
                'status' => 500,
                'error' => true,
                'messages' => $model->errors(),
            ])->setStatusCode(500);
        } else {
            return $this->response->setJSON([
                'status' => 400,
                'error' => true,
                'messages' => $this->validator->getErrors(),
            ])->setStatusCode(400);
        }
    }

    // Update an existing task
    public function update($id = null)
    {
        $model = new Task();   
        // Fetch the task by ID
        $task = $model->find($id);

        if (!$task) {
            return $this->response->setJSON([
                'status' => 404,
                'error' => true,
                'messages' => "Task with id $id not found.",
            ])->setStatusCode(404);
        }

        // Define validation rules for PUT request
        $putRules = [
            'title' => 'required',
            'description' => 'permit_empty',
            'status' => 'required|in_list[pending,in-progress,completed]',
            'due_date' => 'permit_empty|valid_date'
        ];

        // Define validation rules for PATCH request
        $patchRules = [
            'title' => 'permit_empty',
            'description' => 'permit_empty',
            'status' => 'permit_empty|in_list[pending,in-progress,completed]',
            'due_date' => 'permit_empty|valid_date'
        ];

        // Check HTTP method
        $method = $this->request->getServer('REQUEST_METHOD');
        $rules = ($method === 'PUT') ? $putRules : $patchRules;

        // Validate the input data
        $input =  $this->request->getJSON(true);
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 400,
                'error' => true,
                'messages' => $this->validator->getErrors(),
            ])->setStatusCode(400);
        }

        // Update the task
        if ($model->update($id, $input)) {
            $updatedTask = $model->find($id);
            return $this->response->setJSON([
                'status' => 200,
                'error' => false,
                'data' => $updatedTask,
            ])->setStatusCode(200);
        }
        return $this->response->setJSON([
            'status' => 500,
            'error' => true,
            'messages' => 'Error occurred while updating task.',
        ])->setStatusCode(500);
    }

    // Delete a task
    public function delete($id = null)
    {
        $model = new Task(); 
        if (!$model->find($id)) {
            return $this->response->setJSON([
                'status' => 404,
                'error' => true,
                'messages' => "Task with id $id not found.",
            ])->setStatusCode(404);
        }

        if ($model->delete($id)) {
            return $this->response->setJSON([
                'status' => 200,
                'error' => false,
                'messages' => 'Task deleted successfully.',
            ])->setStatusCode(200);
        }
        return $this->response->setJSON([
            'status' => 500,
            'error' => true,
            'messages' => 'Error occurred while deleting task.',
        ])->setStatusCode(500);
    }
}
