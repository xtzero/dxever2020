<?php


namespace App\Support;


class AjaxSupport
{
    /**
     * ajax 方法
     */
    public static function ajax()
    {
        $params = func_get_args();
        switch (count($params)) {
            case 0: {
                return json_encode([
                    'code' => 200,
                    'msg' => 'suc',
                    'data' => []
                ]);
            }
            case 1: {
                if (is_array($params[0])) {
                    return json_encode([
                        'code' => 200,
                        'msg' => 'suc',
                        'data' => $params[0]
                    ]);
                }
                if (is_string($params[0])) {
                    return json_encode([
                        'code' => 500,
                        'msg' => $params[0],
                        'data' => []
                    ]);
                }
            }break;

            case 2: {
                if (is_array($params[0])) {
                    return json_encode([
                        'data' => $params[0],
                        'code' => $params[1],
                        'msg' => '成功'
                    ]);
                }
                if (is_string($params[0])) {
                    return json_encode([
                        'data' => [],
                        'code' => $params[1],
                        'msg' => $params[0]
                    ]);
                }
            }break;

            case 3: {
                json_encode([
                    'data' => $params[0],
                    'msg' => $params[1],
                    'code' => $params[2]
                ]);
            }
        }
    }
}
