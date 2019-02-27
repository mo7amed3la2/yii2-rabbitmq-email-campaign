<?php declare(strict_types=1);

pcntl_signal(SIGHUP, function($signo){
	echo "1\n";

	echo "2\n";
});

while(true){
	pcntl_signal_dispatch();
}
