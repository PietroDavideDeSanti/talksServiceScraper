<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\HttpClient\HttpClient;


class CheckUrlCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:checkUrl';

    protected function configure()
    {
        $this->setDescription('command that has to check if the file ads.txt exists')
             ->addArgument('url', InputArgument::REQUIRED, 'enter URL')
        ;
    
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $url = $input->getArgument('url');

        $arr_url = explode("/",$url); 
        $host_name = "";
        
        if(isset($arr_url[2])){
            $host_name = $arr_url[0]."//".$arr_url[2];
        }else{
            $host_name = "https://".$url;
        }

           
        //make a request
        $client = HttpClient::create();
        $response = $client->request('GET', $host_name."/ads.txt");

        $statusCode = $response->getStatusCode();

        $message = "";

        if($statusCode == 200){
            $lines = explode("\n",$response->getContent()); 
            
            //function to check if the ads.txt has more than 10 lines
            $result = $this->moreTenLinesCheck($lines);
            if($result){
                $message = "The ads.txt has more than 10 lines";
            }else{
                $message = "The ads.txt has less than 10 lines";
            }

        }else{
            $message = "The given url does not have ads.txt file";
        }

        echo $message;

        return 0;
    }

    private function moreTenLinesCheck($lines){
        $result = false;
        $counter = 0;
        foreach($lines as $line){
            if($counter > 10){
                $result = true;
                break;

            }else{

                if((strlen($line) > 0 && $line[0] != "#") || strlen($line) == 0){
                    $counter++;
                }
            }

        }

        return $result;
    }
}