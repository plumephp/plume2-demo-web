<?php

namespace Ace\Controller;

use Ace\Model\User;
use Plume\Controller;
use Plume\Response;
use Illuminate\Database\Eloquent\Model;

class IndexController extends Controller
{
    /**
     * 首页
     */
	public function indexAction()
	{
        $count = User::count();
        $page_count = 20;
        $max_page = ceil($count / $page_count);
        
        $page = $this->getRequest()->get('page');
        $page = intval($page);
        
        if ($page < 1) {
            $page = 1;
        }
        
        if ($page > $max_page) {
            $page = $max_page;
        }

        $redis_key = "user_page_{$page}_limit_{$page_count}";
        $redis_provider = $this->getContainer()->get('plume.redis');

        $users = $redis_provider->get($redis_key);

        if ($users == null) {
            //$users = User::whereBetween('id', [($page - 1) * $page_count,  ($page * $page_count)])->get();
            $users = User::where('id', '>', ($page - 1) * $page_count)->take($page_count)->get();
            $redis_provider->set($redis_key, serialize($users));
        } else {
            $users = unserialize($users);
        }

        $update_token = md5(rand());

        $_SESSION['update_token'] = $update_token;
        return $this->render('@Ace/index.html', ['users' => $users, 'count' => $count, 'page_count' => $page_count, 'page' => $page, 'max_page' => $max_page, 'update_token' => $update_token]);
	}

    /**
     * 更新 User
     */
    public function updateAction()
    {
        $response = new Response;

        $token = $this->getContainer()->getRequest()->get('update_token');

        if (!$token == $_SESSION['update_token']) {
            $response->headers->set('Status', '403 Not Allow');
            $response->json(json_encode(['message' => 'Not Allow', 'token']));
            return $response;
        }

        $id = $this->getContainer()->getRequest()->get('id');
        $username = $this->getContainer()->getRequest()->get('username');
        $password = $this->getContainer()->getRequest()->get('password');

        $user = User::find($id);

        if ($user == null) {
            $response->json(['ok' => false]);
            return $response;
        }

        $user->username = $username;
        $user->password = $password;
        $user->save();

        $response->json(['ok' => true]);
        return $response;
    }

    /**
     * 创建一个User
     * 允许访问的方法是 GET 和 POST
     * 其他方法返回 405 Method Not Allowed
     * 当请求方法为 GET 时渲染视图
     * 当请求方法为 POST 时处理客户端提交的数据
     */
    public function createAction()
    {
        $request = $this->getContainer()->getRequest();
        $method = strtolower($request->getMethod());

        if ($method === 'get') {
            $_SESSION['create_token'] = $create_token = md5(rand());
            return $this->render('@Ace/create.html', ['create_token' => $create_token]);
        } else if ($method === 'post') {
            $response = new Response;

            $create_token = $request->get('create_token');
            if ($create_token == null || $create_token !== $_SESSION['create_token']) {
                $response->headers->set('content-type', 'application/json');
                $response->headers->set('status', '403 Not Allowed');
                return $response;
            }

            /**
             * 此处没有对数据作校验
             * 即：假设所有数据都是正确的
             */
            $username = $request->get('username');
            $password = $request->get('password');
            $user =  new User;
            $user->username = $username;
            $user->password = $password;

            $ok = false;

            try {

                if (!$username || !$password) {
                    throw new \Exception("username or password is null");
                }

                if ($user->save()) {
                    $ok = true;
                    $this->getContainer()->get('logger')->setLogFile('insert_error')->info($user);

                }
            } catch (\Exception $e) {
                $ok = false;
                $this->getContainer()->get('logger')->setLogFile('insert_error')->error($e);
                $this->getContainer()->get('logger')->reset();
            }
            return $this->render('@Ace/complete.html', ['ok' => $ok]);;
        } else {
            $response = new Response();
            $response->headers->set('status', '405 Method Not Allowed');
            $response->headers->set('content-type', 'application/json');
            return $response;
        }
    }

    /**
     * 如何注册和使用Provider
     * @return Array
     */
    public function nAction()
    {
        $this->getContainer()->register(new \Ace\Provider\DemoProvider());
        $demo_provider = $this->getContainer()->get('provider.demo');
        $demo_provider->sayHello("Kitty");
        return $this->render('@Ace/404.html');
    }

    public function resAction()
    {
        return new Response("Hello");
    }

    public function singleAction()
    {
        $key = 'user_id_10';

       $redis_provider = $this->getContainer()->get('plume.redis');

        $user = $redis_provider->get($key);

        if ($user == null) {
            $user = User::find(10);
            $redis_provider->set($key, serialize($user));
        } else {
            $user = unserialize($user);
        }

        $response = new Response();
        $response->json(['user' => $user]);

        return $response;
    }

}

?>
