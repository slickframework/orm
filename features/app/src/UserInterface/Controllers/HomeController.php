<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Features\App\UserInterface\Controllers;

use Features\App\Domain\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Slick\Http\Message\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * HomeController
 *
 * @package Features\App\UserInterface\Controllers
 */
final class HomeController
{

    #[Route(path: '/{userId}', name: 'home', methods: ['GET'])]
    public function handle(UserRepository $users, string $userId): ResponseInterface
    {
        $user = $users->withId(intval($userId));
        $type = get_debug_type($user->email());
        return new Response(
            200,
            '
<h2>It works!</h2>
<h3>Welcome, '.$user->name().' &lt;'.$user->email().'&gt;</h3>
<p>Email is type: '.$type.'</p>',
            ['content-type' => 'text/html']
        );
    }
}
