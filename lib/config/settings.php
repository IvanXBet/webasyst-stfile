<?php
return array(
	'hook_mode' => array(
        'title' => 'Переключение между хуком и статической функцией',
        'value' => array(
            'value' => 0,
            'title' => 'static function',
        ),
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
            array(
                'value' => 0,
                'title' => 'static function',
            ),
            array(
                'value' => 1,
                'title' => 'hook',
            ),
        ),
    ),
);
