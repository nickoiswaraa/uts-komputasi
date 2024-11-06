<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function evaluateExpression($expression, $x) {
    $expression = str_replace('x', (string)$x, $expression);
    $expression = preg_replace('/(\d+(?:\.\d+)?)\s*\^/i', 'pow($1,', $expression);
    $expression = preg_replace('/\^/i', ',', $expression);
    $expression = preg_replace('/(?<=\d|\))(?=\()/', '*', $expression);

    $tokens = preg_split('/([\+\-\*\/$$$$,])/', $expression, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    $output = [];
    $operators = [];
    $precedence = ['+' => 1, '-' => 1, '*' => 2, '/' => 2, 'pow' => 3];

    foreach ($tokens as $token) {
        if (is_numeric($token)) {
            $output[] = $token;
        } elseif ($token == '(') {
            $operators[] = $token;
        } elseif ($token == ')') {
            while (!empty($operators) && end($operators) != '(') {
                $output[] = array_pop($operators);
            }
            if (!empty($operators) && end($operators) == '(') {
                array_pop($operators);
            }
        } elseif (in_array($token, ['+', '-', '*', '/']) || $token == 'pow') {
            while (!empty($operators) && end($operators) != '(' && $precedence[$token] <= $precedence[end($operators)]) {
                $output[] = array_pop($operators);
            }
            $operators[] = $token;
        } elseif ($token == ',') {
            while (!empty($operators) && end($operators) != '(') {
                $output[] = array_pop($operators);
            }
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
            if (count($stack) < 2) {
                throw new Exception("Invalid expression: not enough operands for operator $token");
            }
            $b = array_pop($stack);
            $a = array_pop($stack);
            switch ($token) {
                case '+': $stack[] = $a + $b; break;
                case '-': $stack[] = $a - $b; break;
                case '*': $stack[] = $a * $b; break;
                case '/': 
                    if ($b == 0) {
                        throw new Exception("Division by zero");
                    }
                    $stack[] = $a / $b; 
                    break;
            }
        } elseif ($token == 'pow') {
            if (count($stack) < 2) {
                throw new Exception("Invalid expression: not enough operands for power operation");
            }
            $b = array_pop($stack);
            $a = array_pop($stack);
            $stack[] = pow($a, $b);
        }
    }

    if (count($stack) != 1) {
        throw new Exception("Invalid expression: too many values left on the stack");
    }

    return $stack[0];
}

function newtonForwardInterpolation($x, $y, $xp) {
    $n = count($x);
    $f = array_fill(0, $n, array_fill(0, $n, 0));

    for ($i = 0; $i < $n; $i++) {
        $f[$i][0] = $y[$i];
    }

    for ($i = 1; $i < $n; $i++) {
        for ($j = 0; $j < $n - $i; $j++) {
            $f[$j][$i] = ($f[$j + 1][$i - 1] - $f[$j][$i - 1]) / ($x[$j + $i] - $x[$j]);
        }
    }

    $result = $f[0][0];
    $term = 1;
    for ($i = 1; $i < $n; $i++) {
        $term *= ($xp - $x[$i - 1]);
        $result += $f[0][$i] * $term;
    }

    return $result;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $method = $_POST['method'];

    if ($method === 'forward') {
        $xValues = array_map('floatval', explode(',', $_POST['xValues']));
        $yValues = array_map('floatval', explode(',', $_POST['yValues']));
        $xInterpolate = floatval($_POST['xInterpolate']);

        $interpolatedValue = newtonForwardInterpolation($xValues, $yValues, $xInterpolate);

        echo json_encode([
            'method' => 'forward',
            'interpolatedValue' => $interpolatedValue,
            'xValues' => $xValues,
            'yValues' => $yValues,
            'xInterpolate' => $xInterpolate
        ]);
    } elseif ($method === 'raphson') {
        $function = $_POST['function'];
        $derivative = $_POST['derivative'];
        $initialGuess = floatval($_POST['initialGuess']);
        $tolerance = floatval($_POST['tolerance']);
        $maxIterations = intval($_POST['maxIterations']);

        try {
            $result = newtonRaphson($function, $derivative, $initialGuess, $tolerance, $maxIterations);
            $result['method'] = 'raphson';
            $result['input'] = [
                'function' => $function,
                'derivative' => $derivative,
                'initialGuess' => $initialGuess,
                'tolerance' => $tolerance,
                'maxIterations' => $maxIterations
            ];
            echo json_encode($result);
        } catch (Exception $e) {
            echo json_encode(['error' => 'Error in calculation: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['error' => 'Invalid method selected.']);
    }
}