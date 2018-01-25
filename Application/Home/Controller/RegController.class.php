<?php
namespace Home\Controller;

use Common\Controller\HomeBaseController;
use Think\Verify;
/**
 * 注册首页Controller
 */
class RegController extends HomeBaseController
{
    /**
     * 首页
     */
    public function selecttype()
    {
        if (IS_POST) {
            $param = I('post.');
//            $this->redirect('Home/Reg/register', ['type' => $param['type']]);
            $this->assign('type', $param['type']);
            $this->display('Reg/register');
        } else {
            $this->display();
        }
    }

    public function reg()
    {
        if (IS_POST) {
            $param = I('post.');
            $this->redirect('Reg/register', ['type' => $param['type']]);
        } else {
            $this->display();
        }
    }

    public function agreement()
    {
        $this->display();
    }
}

