<?php
/**
 * controller/Traits/AdminProblem.php @ XenOnline
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

namespace Controller\Traits;
use \Facade\DB;
use \Facade\View;
use \Facade\Request;
use \Facade\Site;
use \Model\Problem;

trait AdminProblem
{
    public $problem_post_items = [
        'title' => 'title',
        'content' => 'content',
        'content_html' => 'editormd-html-code',
        'tag' => 'tag',
        'time_limit' => 'time_limit',
        'mem_limit' => 'mem_limit'
    ];

    private $problem_post_error = [
        'title' => '必须输入标题！',
        'content' => '必须输入题面内容！',
        'content_html' => '未找到生成的题面HTML！',
        'time_limit' => '请输入每组数据限时！',
        'mem_limit' => '请输入内存限制！'
    ];

    public function problemList($page = 1)
    {
        // TODO: 分页
        $page = (int)$page;
        $skip = ($page - 1) * self::$piece_per_page;
        $limit = self::$piece_per_page;
        if ($page < 1 || $skip > $this->count['problem']) {
            View::error404();
        }

        $problems = Problem::findMany([], ['skip'=>$skip, 'limit'=>$limit])->toArray();

        View::assign('problems', $problems);
        $this->show('admin/problem.list');
    }

    public function problemPost()
    {
        $problem = $this->getPostedProblem();
        $files = $this->getPostedProblemData($problem);

        $file_dir = PUBLICDIR.'data/';

        if (!$problem['id'] && !$files) {
            /* ERROR: New problems must have data */
            $this->info('danger', '必须上传测试数据！');
            View::assign('problem', $problem);
            $this->show('admin/problem.edit');
            exit();
        } elseif ($problem['id']) {
            /* Existing problems with new data */
            $existing_problem = Problem::find(
                [
                    'id' => (int)$problem['id']
                ]
            );
            if (!$existing_problem) {
                /* ERROR: Wrong problem ID */
                $this->info('danger', '尝试修改不存在的题目！', 'session');
                Site::go('/admin/problem');
            }
            foreach ($this->problem_post_items as $key => $_) {
                $existing_problem[$key] = $problem[$key];
            }
            // Update data?
            if ($files) {
                $existing_problem['turn'] = count($files['input']);
                if (!$this->saveProblemTestData($file_dir.$existing_problem['hash'], $files)) {
                    /* ERROR: Failed to save test data */
                    $this->info('danger', '测试数据保存失败，题目未发布');
                    View::assign('problem', $problem);
                    $this->show('admin/problem.edit');
                    exit();
                }
            }
            $existing_problem->save();
            $this->info('success', '修改题目成功！题号#'.$problem['id'], 'session');
            Site::go('/admin/problem');
        } elseif (!$problem['id']) {
            /* New problems */
            $problem['id'] = DB::autoinc('problem');
            $problem['turn'] = count($files['input']);
            $problem->save();
            $problem = Problem::find(
                [
                    '_id' => $problem->getID(true)
                ]
            );
            // Save test data
            if (!$this->saveProblemTestData($file_dir.$problem['hash'], $files)) {
                /* ERROR: Failed to save test data */
                $this->info('danger', '测试数据保存失败，但题目已发布');
                View::assign('problem', $problem);
                $this->show('admin/problem.edit');
            } else {
                $this->info('success', '添加题目成功！题号#'.$problem['id'], 'session');
                Site::go('/admin/problem');
            }
        }

    }

    public function problemAddPage()
    {
        $this->show('admin/problem.edit');
    }

    public function problemEditPage($id)
    {
        $problem = Problem::find(
            [
                'id' => 1
            ]
        );
        if (!$problem) {
            View::error404();
        }
        View::assign('problem', $problem);
        $this->show('admin/problem.edit');
    }

    public function problemSearch()
    {
        $filter = [];

        $redirect = "location: /admin/problem";
        $user_filter = Request::post('filter');
        $info = '<div class="alert alert-info" role="alert">';

        if (!$user_filter) {
            Site::go($redirect);
        }

        switch (Request::post('type')) {
            case 'id':
                $filter['id'] = (int)$user_filter;
                $info .= "<b>Search[ID]:</b> #";
                break;
            case 'title':
                $filter['title'] = DB::regex('.*'.$user_filter.'.*');
                $info .= "<b>Search[Title]:</b> ";
                break;
            case 'tag':
                $filter['tag'] = $user_filter;
                $info .= "<b>Search[Tag]:</b> ";
                break;
            default:
                Site::go($redirect);
        }

        $rs = Problem::findMany($filter)->toArray();
        $info .= "$user_filter</div>";

        View::assign('info', $info);
        View::assign('problems', $rs);

        $this->show('admin/problem.list');
    }

    private function saveProblemTestData($dir, $files)
    {
        echo $dir;
        if (!mkdir($dir) && !file_exists($dir)) {
            return false;
        }
        foreach($files['input'] as $n => $file) {
            if (!move_uploaded_file($file, $dir.'/'.$n.'.in')) {
                return false;
            }
        }
        foreach($files['stdout'] as $n => $file) {
            if (!move_uploaded_file($file, $dir.'/'.$n.'.out')) {
                return false;
            }
        }

        return true;
    }

    private function getPostedProblemData($problem)
    {
        $files = [];

        if (!isset($_FILES['input'], $_FILES['stdout'])) {
            return null;
        }

        if (count($_FILES['input']) != count($_FILES['stdout'])) {
            $this->info('danger', '测试数据上传错误：选择错误');
            View::assign('problem', $problem);
            $this->show('admin/problem.edit');
            exit();
        }

        $info = '';
        foreach ($_FILES["input"]["error"] as $key => $error) {
            if ($error != UPLOAD_ERR_OK) {
                $name = $_FILES["input"]["name"][$key];
                $info .= '<br>[Input]'.$name;
            } else {
                $files['input'][] = $_FILES["input"]["tmp_name"][$key];
            }
        }
        foreach ($_FILES["stdout"]["error"] as $key => $error) {
            if ($error != UPLOAD_ERR_OK) {
                $name = $_FILES["stdout"]["name"][$key];
                $info .= '<br>[Output]'.$name;
            } else {
                $files['stdout'][] = $_FILES["stdout"]["tmp_name"][$key];
            }
        }
        if ($info) {
            $this->info('danger', '测试数据上传错误：'.$info);
            View::assign('problem', $problem);
            $this->show('admin/problem.edit');
            exit();
        }

        return $files;
    }
    
    private function getPostedProblem()
    {
        // Fetch the problem posted
        $problem = Problem::one();
        $info = '';
        foreach ($this->problem_post_items as $key => $value) {
            $problem[$key] = Request::post($value);
            // Error only when it's an error
            if (!$problem[$key] && isset($this->problem_post_error[$key])) {
                $info .= '<br>'.$this->problem_post_error[$key];
            }
        }
        $problem['tag'] = explode(',', $problem['tag']);

        // Whether to edit some existing one
        $former_id = Request::post('pid');
        if ($former_id) {
            $edit = true;
            $problem['id'] = $former_id;
        } else {
            $edit = false;
        }

        // If necessary fields aren't filled
        if ($info) {
            $info = '<b>缺少必要信息：</b>'.$info;
            $this->info('danger', $info);
            View::assign('problem', $problem);
            $this->show('admin/problem.edit');
            exit();
        }

        return $problem;
    }
}