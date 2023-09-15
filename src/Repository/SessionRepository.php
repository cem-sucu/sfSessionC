<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

//    /**
//     * @return Session[] Returns an array of Session objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Session
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


// AFFICHER LES STAGIAIRE NON INSCRIT
public function findNonInscrits($session_id)
{
    $em = $this->getEntityManager();
    $sub = $em->createQueryBuilder();

    $qb = $sub;
    // selectionner tout les stagiaire d'une session dont l'id est passé en parametre
    $qb->select('s')
        ->from('App\Entity\Stagiaire', 's')
        ->leftJoin('s.sessions', 'se')
        ->where('se.id = :id');

    $sub = $em->createQueryBuilder();
    // selectionner tous les stagiaire qui ne SONT PAS (NOT IN dans le résultat précédent)
    // on obtient donc les stagaiire non inscrit pour une session définie
    $sub->select('st')
        ->from('App\Entity\Stagiaire', 'st')
        ->where($sub->expr()->notIn('st.id', $qb->getDQL()))
        // requete parametré
        ->setParameter('id', $session_id)
        //trier la liste des stagaire sur le nom de famille
        ->orderBy('st.nom');

    //renvoyer le résultat
    $query = $sub->getQuery();
    return $query->getResult();
}

//AFFICHER LES MODULES DISPONIBLE MAIS NON PRESENT SUR LA SESSION
public function findModuleDispo($session_id)
{
    $em = $this->getEntityManager();

    // Sélectionner tous les modules d'une session dont l'ID est passé en paramètre
    $qb = $em->createQueryBuilder();
    $qb->select('m')
        ->from('App\Entity\Module', 'm')
        ->leftJoin('m.programmes', 'p')
        ->leftJoin('p.session', 's')
        ->where('s.id = :id');
        
        // Sélectionner tous les modules qui ne SONT PAS dans le résultat précédent
        $sub = $em->createQueryBuilder();
        $sub->select('mo')
        ->from('App\Entity\Module', 'mo')
        ->where($sub->expr()->notIn('mo.id', $qb->getDQL()))
        // Trier la liste des modules par le nom
        ->setParameter('id', $session_id);
        $sub->orderBy('mo.intitulee');

    // Renvoyer le résultat
    $query = $sub->getQuery();
    return $query->getResult();
}

}
