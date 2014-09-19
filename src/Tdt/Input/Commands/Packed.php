<?php

namespace Tdt\Input\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Job;

class Packed extends Command
{

    /**
     * The console command name
     *
     * @var string
     */
    protected $name = 'packed';

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = "Start a data load job.";

    /**
     * Execute the console command
     *
     * @return void
     */
    public function fire()
    {
        $this->comment("\nPACKED DATALOADER");

        $jobs = Job::orderBy('collection_uri', 'asc')
                   ->orderBy('name', 'asc')
                   ->get();

        if ($this->argument('all')) {
            foreach ($jobs as $job) {
                $job_uri = $job->collection_uri . '/' . $job->name;
                try {
                    $this->call('input:execute', array('jobname' => $job_uri));


                    $this->question("\n\n***********************\n");
                    $this->question("Job complete! Next job!\n");
                    $this->question("***********************\n");
                } catch(\ErrorException $e) {
                    $this->error("\n\n***********************\n");
                    $this->error("Something went wrong while running the job $job_uri\n");
                    $this->error($e->getMessage());
                    $this->error("\nSkipping and starting next job!");
                    $this->error("\n***********************\n");
                }
            }
            $this->question("\n\nAll jobs completed!");
        } else {
            $this->info("\nAvailable jobs:");
            $availableJobs = array();

            $i = 1;
            foreach ($jobs as $job) {
                $job = $job->collection_uri . '/' . $job->name;
                array_push($availableJobs, $job);

                $this->line(" • " . $i . ") " . $job);
                $i++;
            }

            $keys = array_keys($availableJobs);

            $number = $this->ask("\nWhat job should I start? (number): ");
            while (!in_array($number - 1, $keys)) {
                $this->error("Sorry, that was not a valid number, try again!");
                $number = $this->ask("What job should I start? (number): ");
            }

            $jobPicked = $availableJobs[$number - 1];
            $this->info("\nStarting job #$number ($jobPicked):");

            $this->call('input:execute', array('jobname' => $jobPicked));

            $this->question("\n\nJob complete! Re-run the command to start a new job.\n");
        }
    }

    /**
     * Get the console command arguments
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('all', InputArgument::OPTIONAL, 'Run all the jobs?'),
        );
    }

    /**
     * Get the console command options
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(

        );
    }
}
