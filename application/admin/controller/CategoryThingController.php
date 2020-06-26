<?php
/**
 * 报事分类控制器
 */

namespace app\admin\controller;

use think\Request;
use app\common\model\CategoryThing;

use app\common\validate\CategoryThingValidate;

class CategoryThingController extends Controller
{

    //列表
    public function index(Request $request, CategoryThing $model)
    {

        $data = $this->getTreeList($model);

        $this->assign([
            'data' => $data,
        ]);
        return $this->fetch();
    }

    //添加
    public function add(Request $request, CategoryThing $model, CategoryThingValidate $validate)
    {
        if ($request->isPost()) {
            $param           = $request->param();
            $validate_result = $validate->scene('add')->check($param);
            $param['admin_user_id'] = $this->uid;
            if (!$validate_result) {
                return error($validate->getError());
            }

            $result = $model::create($param);

            $url = URL_BACK;
            if (isset($param['_create']) && $param['_create'] == 1) {
                $url = URL_RELOAD;
            }

            return $result ? success('添加成功', $url) : error();
        }

        $this->assign([
            'department_list' => $this->getSelectList($model)
        ]);

        return $this->fetch();
    }

    //修改
    public function edit($id, Request $request, CategoryThing $model, CategoryThingValidate $validate)
    {

        $data = $model::get($id);
        if ($request->isPost()) {
            $param           = $request->param();
            $validate_result = $validate->scene('edit')->check($param);
            if (!$validate_result) {
                return error($validate->getError());
            }

            $result = $data->save($param);
            return $result ? success() : error();
        }

        $this->assign([
            'data'            => $data,
            'department_list' => $this->getSelectList($model, $data->parent_id)

        ]);
        return $this->fetch('add');

    }

    //删除
    public function del($id, CategoryThing $model)
    {
        if (count($model->noDeletionId) > 0) {
            if (is_array($id)) {
                if (array_intersect($model->noDeletionId, $id)) {
                    return error('ID为' . implode(',', $model->noDeletionId) . '的数据无法删除');
                }
            } else if (in_array($id, $model->noDeletionId)) {
                return error('ID为' . $id . '的数据无法删除');
            }
        }

        if ($model->softDelete) {
            $result = $model->whereIn('id', $id)->useSoftDelete('delete_time', time())->delete();
        } else {
            $result = $model->whereIn('id', $id)->delete();
        }

        return $result ? success('操作成功', URL_RELOAD) : error();
    }

}
