<?php

function evaluateExpression($expression, $x) {
    $expression = str_replace('x', $x, $expression);
    $expression = preg_replace('/(\d+)\s*\^/i', 'pow($1,', $expression);
    $expression = str_replace('^', ',', $expression);
    $expression = preg_replace('/(?<=\d|\))(?=\()/', '*', $expression);
    
    $tokens = preg_split('/([\+\-\*\/$$$$])/', $expression, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    $output = [];
    $operators = [];
    $precedence = ['+' => 1, '-' => 1, '*' => 2, '/' => 2, '^' => 3];

    foreach ($tokens as $token) {
        if (is_numeric($token)) {
            $output[] = $token;
        } elseif ($token == '(') {
            $operators[] = $token;
        } elseif ($token == ')') {
            while (end($operators) != '(') {
                $output[] = array_pop($operators);
            }
            array_pop($operators);
        } elseif (in_array($token, ['+', '-', '*', '/', '^'])) {
            while (!empty($operators) && end($operators) != '(' && $precedence[$token] <= $precedence[end($operators)]) {
                $output[] = array_pop($operators);
            }
            $operators[] = $token;
        } elseif (strpos($token, 'pow') !== false) {
            $operators[] = $token;
        }
    }
    while (!empty($operators)) {
        $output[] = array_pop($operators);
    }

    $stack = [];
    foreach ($output as $token) {
        if (is_numeric($token)) {
            $stack[] = $token;
        } elseif (in_array($token, ['+', '-', '*', '/'])) {
            $b = array_pop($stack);
            $a = array_pop($stack);
            switch ($token) {
                case '+': $stack[] = $a + $b; break;
                case '-': $stack[] = $a - $b; break;
                case '*': $stack[] = $a * $b; break;
                case '/': $stack[] = $a / $b; break;
            }
        } elseif (strpos($token, 'pow') !== false) {
            $b = array_pop($stack);
            $a = substr($token, 4, -1);
            $stack[] = pow($a, $b);
        }
    }
    return $stack[0];
}

function newtonRaphson($f, $df, $x0, $tolerance, $max_iterations) {
    $x = $x0;
    $iterations = [];

    for ($i = 0; $i < $max_iterations; $i++) {
        $fx = evaluateExpression($f, $x);
        $dfx = evaluateExpression($df, $x);

        if (abs($dfx) < $tolerance) {
            return ['error' => 'Derivative too close to zero. Choose a different initial guess.'];
        }

        $x_new = $x - $fx / $dfx;
        $iterations[] = ['iteration' => $i + 1, 'x' => $x_new, 'fx' => evaluateExpression($f, $x_new)];

        if (abs($x_new - $x) < $tolerance) {
            return ['root' => $x_new, 'iterations' => $iterations];
        }

        $x = $x_new;
    }

    return ['error' => 'Maximum iterations reached without convergence.'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $method = $_POST['method'];

    if ($method === 'raphson') {
        $function = $_POST['function'];
        $derivative = $_POST['derivative'];
        $initialGuess = floatval($_POST['initialGuess']);
        $tolerance = floatval($_POST['tolerance']);
        $maxIterations = intval($_POST['maxIterations']);

        $result = newtonRaphson($function, $derivative, $initialGuess, $tolerance, $maxIterations);
        $result['method'] = 'raphson';

        echo json_encode($result);
    } else {
        echo json_encode(['error' => 'Invalid method selected.']);
    }
}