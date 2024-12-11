<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[
    OA\Info(
        title: 'Library Management System API',
        version: '1.0.0',
        description: 'Technical test for backend developer position at PT. Altech Omega Andalan. Simple RESTful API for a library management system.',
    ),
    OA\Server(url: 'http://localhost:8000/api/v1', description: 'Local V1'),
    OA\Contact(
        name: 'Abel Ardhana Simanungkalit',
        url: 'https://github.com/abela-a',
        email: 'abelardhana96@gmail.com',
    )
]
abstract class Controller
{
    //
}
