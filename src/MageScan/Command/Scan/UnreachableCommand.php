<?php
/**
 * Mage Scan
 *
 * PHP version 5
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */

namespace MageScan\Command\Scan;

use MageScan\Check\UnreachablePath;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Scan unreachable path command
 *
 * @category  MageScan
 * @package   MageScan
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magescan
 */
class UnreachableCommand extends AbstractCommand
{
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('scan:unreachable')
            ->setDescription('Check unreachable paths');
        parent::configure();
    }

    /**
     * Execute command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $unreachablePath = new UnreachablePath;
        $unreachablePath->setRequest($this->request);
        $results = $unreachablePath->checkPaths($this->url);

        if ($input->getOption('json')) {
          $cleanOut=[];
          foreach ($results as $r) {
            $cleanOut[$r[0]] = ["http_status"=>$r[1],"status"=>$r[2]?"Pass":"Fail"];
          }
          $output->write(json_encode($cleanOut));
        } else {
          foreach ($results as &$result) {
              if ($result[2] === false) {
                  $result[2] = '<error>Fail</error>';
              } elseif ($result[2] === true) {
                  $result[2] = '<bg=green>Pass</bg=green>';
              }
          }

          $this->writeHeader('Unreachable Path Check');
          $table = new Table($this->output);
          $table
              ->setHeaders(array('Path', 'Response Code', 'Status'))
              ->setRows($results)
              ->render();
        }
    }
}
