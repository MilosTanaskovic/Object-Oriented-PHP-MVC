<?php 

class Posts extends Controller {

    public function __construct(){
        if(!isLoggedIn()){
            redirect('users/login');
        }

        $this->postModel = $this->model('Post');
        $this->userModel = $this->model('User');
    }

    public function index(){
        // Get posts
        $posts = $this->postModel->getPosts();

        $data = [
            'posts' => $posts
        ];

        $this->view('posts/index', $data);
    }

    public function add() {

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Sanitaze POST array
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'user_id' => $_SESSION['user_id'],
                'title' => trim($_POST['title']),
                'body' => trim($_POST['body']),
                'title_err' => '',
                'body_err' => ''
            ];

            // Validate title
            if(empty($data['title'])){
                $data['title_err'] = 'Please enter title';
            }
            if(empty($data['body'])){
                $data['body_err'] = 'Please enter body text';
            }

            // Make sure no errors
            if(empty($data['title_err']) && empty($data['body_err'])){
                // Validated
                if($this->postModel->addPost($data)){
                    flash('post_message', 'Post added');
                    redirect('posts');
                } else {
                    die('wrong');
                }
                
            }else {
                // Load view with error
                $this->view('posts/add', $data);
            }
        }else {

        }

        $data = [
            'title' => '',
            'body' => ''
        ];

        $this->view('posts/add', $data);
    }

    public function show($id){
        $post = $this->postModel->getPostById($id);
        $user = $this->userModel->getUserById($post->user_id);
        $data = [
            'post' => $post,
            'user' => $user
        ];
        $this->view('posts/show', $data);
    }

    public function edit($id) {

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Sanitaze POST array
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'id' => $id,
                'user_id' => $_SESSION['user_id'],
                'title' => trim($_POST['title']),
                'body' => trim($_POST['body']),
                'title_err' => '',
                'body_err' => ''
            ];

            // Validate title
            if(empty($data['title'])){
                $data['title_err'] = 'Please enter title';
            }
            if(empty($data['body'])){
                $data['body_err'] = 'Please enter body text';
            }

            // Make sure no errors
            if(empty($data['title_err']) && empty($data['body_err'])){
                // Validated
                if($this->postModel->updatePost($data)){
                    flash('post_message', 'Post Updated');
                    redirect('posts');
                } else {
                    die('wrong');
                }
                
            }else {
                // Load view with error
                $this->view('posts/edit', $data);
            }
        }else {
            //Get existing post for model
            $post = $this->postModel->getPostById($id);
        // Check for owner
        if($post->user_id != $_SESSION['user_id']){
            redirect('posts');
        }

        $data = [
            'id' => $id,
            'title' => $post->title,
            'body' => $post->body
        ];

        $this->view('posts/edit', $data);
        }
    }
}