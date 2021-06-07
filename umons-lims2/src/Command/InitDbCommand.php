<?php

namespace App\Command;

use App\Entity\Hazard;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class InitDbCommand extends Command
{
    protected static $defaultName = 'app:init-db';
    protected static $defaultDescription = 'Initialize database data and create the superuser';

    private EntityManagerInterface $em;
    private UserPasswordEncoderInterface $passwordEncoder;


    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct();
        $this->em = $em;
        $this->passwordEncoder =$passwordEncoder;
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $io = new SymfonyStyle($input, $output);

        $this->createRolesAndSuperUser($io);
        $this->seedHazardsAndIncompatibilities($io);


        $io->success('Done ');

        return Command::SUCCESS;
    }

    private function seedHazardsAndIncompatibilities(SymfonyStyle $io) {

        $explosive = (new Hazard())
            ->setId('GHS01')
            ->setLabel('Explosive');
        $flammable = (new Hazard())
            ->setId('GHS02')
            ->setLabel('Flammable');
        $oxidizing = (new Hazard())
            ->setId('GHS03')
            ->setLabel('Oxidizing');
        $compressed_gas = (new Hazard())
            ->setId('GHS04')
            ->setLabel('Compressed Gas');
        $corrosive = (new Hazard())
            ->setId('GHS05')
            ->setLabel('Corrosive');
        $toxic = (new Hazard())
            ->setId('GHS06')
            ->setLabel('Toxic');
        $harmful = (new Hazard())
            ->setId('GHS07')
            ->setLabel('Harmful');
        $health_hazard = (new Hazard())
            ->setId('GHS08')
            ->setLabel('Health Hazard');
        $environmental_hazard = (new Hazard())
            ->setId('GHS09')
            ->setLabel('Environmental Hazard');


        $explosive
            ->addIncompatibility($flammable)
            ->addIncompatibility($oxidizing)
            ->addIncompatibility($compressed_gas)
            ->addIncompatibility($corrosive)
            ->addIncompatibility($toxic)
            ->addIncompatibility($health_hazard)
            ->addIncompatibility($environmental_hazard);

        $flammable
            ->addIncompatibility($oxidizing)
            ->addIncompatibility($compressed_gas)
            ->addIncompatibility($corrosive)
            ->addIncompatibility($toxic)
            ->addIncompatibility($health_hazard)
            ->addIncompatibility($environmental_hazard);

        $oxidizing
            ->addIncompatibility($corrosive)
            ->addIncompatibility($toxic)
            ->addIncompatibility($harmful)
            ->addIncompatibility($health_hazard)
            ->addIncompatibility($environmental_hazard);

        $compressed_gas
            ->addIncompatibility($toxic)
            ->addIncompatibility($harmful)
            ->addIncompatibility($health_hazard)
            ->addIncompatibility($environmental_hazard);


        $this->em->persist($explosive);
        $this->em->persist($flammable);
        $this->em->persist($oxidizing);
        $this->em->persist($compressed_gas);
        $this->em->persist($corrosive);
        $this->em->persist($toxic);
        $this->em->persist($harmful);
        $this->em->persist($health_hazard);
        $this->em->persist($environmental_hazard);

        $this->em->flush();

        $io->text("Hazards incompatibilities created.");
    }

    private function createRolesAndSuperUser(SymfonyStyle $io)
    {

        $adminName = $io->ask('Type in the name you want to give to the superuser', 'ULIMS Superuser');
        $adminPassword = $io->askHidden('Type in the password', function ($value) {
            if (trim($value) == '') {
                throw new \Exception('The password cannot be empty');
            }
            if (strlen($value) < 6) {
                throw new \Exception('The password must be at least 6 characters long.');
            }
            return $value;
        });




        $roleSuperuser = (new Role())->setName("superuser");
        $roleAdmin = (new Role())->setName("admin");
        $roleUser = (new Role())->setName("user");

        $io->text("Created roles.");


        $user = (new User())
            ->setRole($roleSuperuser)
            ->setRegistrationNumber("ULIMS_SUPERUSER")
            ->setFirstName($adminName)
            ->setLastName('')
        ;

        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            $adminPassword
        ))
        ;

        $this->em->persist($roleSuperuser);
        $this->em->persist($roleAdmin);
        $this->em->persist($roleUser);
        $this->em->persist($user);

        $this->em->flush();

        $io->text("Created superuser.");


    }
}
