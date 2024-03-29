<?php
// Подключение автозагрузки через composer
require __DIR__ . '/../vendor/autoload.php';
use Slim\Factory\AppFactory;
$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);
$app->get('/', function ($request, $response) {
    $user = array(
        'USER_LOGIN' => 'alphaprint30@gmail.com', #Ваш логин (электронная почта)
        'USER_HASH' => 'b405c72a37e7cf46d75a7ead4936827b87e41b53', #Хэш для доступа к API (смотрите в профиле пользователя)
    );
    $subdomain = 'alphaprint30';
    $link = 'https://' . $subdomain . '.amocrm.ru/private/api/auth.php?type=json';

    $curl = curl_init(); #Сохраняем дескриптор сеанса cURL
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
    curl_setopt($curl, CURLOPT_URL, $link);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($user));
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_COOKIEFILE, dirname
        (__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl, CURLOPT_COOKIEJAR, dirname
        (__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    $out = curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
    curl_close($curl); #Завершаем сеанс cURL

    $code = (int) $code;
    $errors = array(
        301 => 'Moved permanently',
        400 => 'Bad request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not found',
        500 => 'Internal server error',
        502 => 'Bad gateway',
        503 => 'Service unavailable',
    );
    try
    {   if ($code != 200 && $code != 204) {
            throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
        }
    } catch (Exception $E) {
        die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
    }
    $link = 'https://' . $subdomain . '.amocrm.ru/api/v2/leads';
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
    curl_setopt($curl, CURLOPT_URL, $link);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

    $out = curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    $code = (int) $code;
    try
    {
        /* Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке */
        if ($code != 200 && $code != 204) {
            throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
        }
    } catch (Exception $E) {
        die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
    }

    $Response = json_decode($out, true);
    $data = $Response['_embedded']['items'];
    $strWithTask = '';
    $strWithoutTask = '';
    $i = 1;
    $j = 1;
    foreach ($data as $value) {
        if ($value['closest_task_at'] == 0) {
            $strWithoutTask = $strWithoutTask . 'Сделка №' . $i . ' - ' . $value['name'] . ' Задач нет' . "<br />";
            $i += 1;
            $dealId = $value['id'];
            $tasks['add'] = array(
                array(
                    'element_id' => $dealId, #ID сделки
                    'element_type' => 2, #Показываем, что это - сделка, а не контакт
                    'task_type' => 1, #Звонок
                    'text' => 'Сделка без задачи',
                    'responsible_user_id' => 109999,
                    'complete_till_at' => 1375285346,
                ),
            );
            $link = 'https://' . $subdomain . '.amocrm.ru/api/v2/tasks';
            $curl = curl_init(); #Сохраняем дескриптор сеанса cURL

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
            curl_setopt($curl, CURLOPT_URL, $link);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($tasks));
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
            curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            $out = curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
            $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            /* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
            $code = (int) $code;
            try
            {
                #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
                if ($code != 200 && $code != 204) {
                    throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
                }
            } catch (Exception $E) {
                die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
            }
        } else {
            $strWithTask = $strWithTask . 'Сделка №' . $j . ' - ' . $value['name'] . ' Ближайшая задача - ' . date('c',$value['closest_task_at']) . "<br />";
            $j += 1;
        }
}
$response->getBody()->write('Сделки без задач' . "<br />" . $strWithoutTask . "<br />" . 'Сделки с задачами' . "<br />" . $strWithTask);
return $response;
});
$app->run();
