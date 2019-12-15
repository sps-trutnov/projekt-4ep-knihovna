<?php
namespace middlewares;

use dependencyInjection\DependencyProviderInterface;
use controllers\AbstractController;
use domain\user\UserRepositoryInterface;

class AuthorizationMiddleware implements MiddlewareInterface {
    private $_dependencyProvider;
    private $_dependencyConfiguration;
    private $_userRepository;

    public function __construct(DependencyProviderInterface $dependencyProvider, DependencyConfigurationInterface $dependencyConfiguration, UserRepositoryInterface $userRepository) {
        $this->_dependencyProvider = $dependencyProvider;
        $this->_dependencyConfiguration = $dependencyConfiguration;
        $this->_userRepository = $userRepository;
    }

    public function process(callable $next) {
        $userId = $_SESSION["userId"];
        
        if(empty($userId) || $userId == 0){
            header("Location: /authentication/signIn.php?returnUrl=".$_SERVER["REQUEST_URI"]);
            exit();
        }

        $user = $userRepository->getById($userId);
        if($user == null){
            header("Location: /authentication/signIn.php?returnUrl=".$_SERVER["REQUEST_URI"]);
            exit();
        }
        else if($user->getIsLibrarian() == 0){
            header("Location: /uzivatel/");
            exit();
        }
        
        $dependencyConfiguration->for("\domain\user\User")->useInstance($user);
        $next();
    }
}