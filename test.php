<?php

function initMoves($argv)
{
    $moves = array_slice($argv, 1);
    $length = count($moves);
    $moves_without_repeat = array_unique($moves);
    if (count($moves_without_repeat) < $length){
        // print_r("Moves shouldn't be repeated ! \n");
        exit("Moves shouldn't be repeated!\n");
    }
    elseif ($length % 2 === 0 || $length === 1 || $length === 0){
        // print_r("The number of moves must be odd and more than one! \n");
        exit("The number of moves must be odd and more than one! \n");
    }
    else {
        print_r("Let's play? \n");
    }
    return $moves;
}

function replaceKeysValues($moves)
{
   foreach ($moves as $key => $value)
   {
       $key++;
       $keys[] = $key;
   }  
   $moves = array_combine($moves, $keys);
   return $moves;
}   


function makeMove($moves)
{
    $move_key = array_rand($moves, 1);
    $script_move = $moves[$move_key];
    return $script_move;
}

function makeHMAC($script_move)
{
    $bytes = random_bytes(32);
    $secure_key = bin2hex($bytes);
    $HMAC = hash_hmac('sha3-256', $script_move, $secure_key);
    print_r("HMAC: {$HMAC}\n");
    return $secure_key;
}

function instructions($moves)
{
    echo "Available moves: \n";
    foreach ($moves as $key => $value) 
    {
        $key++;
        echo "$key - $value \n";
    }
    echo "0 - exit \n";
    echo "? - help \n";

}

function logicGame($user_move, $script_move, $moves)
{
    $half_of_moves = intdiv(count($moves),2);
    $script_move_key = array_search($script_move, $moves); 
    $shift = $half_of_moves - $script_move_key;
    $half_win = array_slice($moves, -$shift);
    $half_lose = array_slice($moves, 0, -$shift);
    $sorted_moves = array_merge($half_win, $half_lose);
    $sorted_script_move_index = array_search($script_move, $sorted_moves);
    $sorted_user_move_index = array_search($user_move, $sorted_moves);
    if ($sorted_user_move_index < $sorted_script_move_index) {
        print_r("{$user_move} - {$moves[$script_move_key]} \n");
        print_r("You Win!!! \n");
    } 
    elseif ($sorted_user_move_index > $sorted_script_move_index) {
        print_r("{$user_move} - {$moves[$script_move_key]} \n");
        print_r("You Lose!!! \n");
    }
    else {
        print_r("{$user_move} - {$moves[$script_move_key]} \n");
        print_r("Dead Heat!!! \n");
    }
}

function helpTable($moves)
{
    $half_of_moves = intdiv(count($moves),2);
    $length = count($moves) - 1;
    $mask = "|%-10s";
    printf($mask, "Moves");
    foreach ($moves as $move)
    {
        printf($mask, $move);
    }
    echo "\n";
    for ($i = 0; $i <= $length; $i++)
    {
        $format = "+----------";
        $str = str_repeat($format, $length + 2);
        print_r("$str \n");
        printf($mask, $moves[$i]);
        for ($j = 0; $j <= $length; $j++)
        {
            $shift = $half_of_moves - $j;
            $half_win = array_slice($moves, -$shift);
            $half_lose = array_slice($moves, 0, -$shift);
            $sorted_moves = array_merge($half_win, $half_lose);
            $sorted_j_index = array_search($moves[$j], $sorted_moves);
            $sorted_i_index = array_search($moves[$i], $sorted_moves);
            if ($sorted_i_index < $sorted_j_index) {
                printf($mask, "Win");
            } 
            elseif ($sorted_i_index > $sorted_j_index) {
                printf($mask, "Lose");
            }
            else {
                printf($mask, "Draw");
            }
            if ($j == $length) {
                echo "\n";
            }
        }
    }
}

function game($moves)
{
    while (true){
        $script_move = makeMove($moves);
        $secure_key = makeHMAC($script_move);
        instructions($moves);
        $user_move = readline("Enter your move:");
        if ($user_move === "0") {
            break;
        } elseif ($user_move == "?"){
            print_r("Help Table \n");
            helpTable($moves);
            continue;
        }
        $moves_replace = replaceKeysValues($moves);
        if (in_array($user_move, $moves_replace)){
            echo " \n";
        } else {
            echo "Invalid move!!\n";
            continue;
        }
        $user_move = (integer)$user_move;
        $user_move_key = --$user_move;
        $user_move = $moves[$user_move_key];
        print_r("Your move: {$user_move} \n");
        print_r("Script move: {$script_move} \n");
        logicGame($user_move, $script_move, $moves);
        print_r("Secure key: {$secure_key}");
        echo "\n";
        echo "/////////////////////////AGAIN?/////////////////////////";
        echo "\n";
    }
}


$moves = initMoves($argv);
game($moves);
