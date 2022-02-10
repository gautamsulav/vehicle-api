<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Repository\VehicleRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class VehicleController extends AbstractController
{
    private $vehicleRepository;
    private $vehicleType;
    private $columns = array('id', 'dateAdded', 'type', 'msrp', 'year', 'make', 'model', 'miles', 'vin');

    public function __construct(VehicleRepository $vehicleRepository)
    {
        $this->vehicleType = $_ENV['VEHICLES_TYPE'];
        $this->vehicleRepository = $vehicleRepository;
    }

    /**
     * The methods works for pagination, sorting and searching as well
     * Sample Request: /vehicles?page=1&&sort=make&&search[make]=Toyota
     * If the query parameters on the route doesn't match with column name
     * Error Response will be sent.
     * Default Page value is 1
     *
     * @Route("/vehicles", name="vehicle_index", methods={"GET"})
     */
    public function index(Request $request): JsonResponse
    {
        if(!in_array($this->vehicleType,["used", "new"])) {
            return new JsonResponse(['message' => 'Please fix the environment variable'], 400);
        }

        $page = $request->query->get('page') ?? 1;
        $sort = $request->query->get('sort');

        // check for valid column for sort
        if($sort != null) {
            if(!in_array($sort, $this->columns)) {
                return new JsonResponse(['message' => 'Invalid sort type.'], 400);
            }
        }else{
            $sort = 'id';
        }

        //check for valid columns for search
        $search = [];
        if(isset($_GET['search'])) {
            $search = $_GET['search'];
            foreach($search as $key=>$value) {
                if(!in_array($key, $this->columns)) {
                    return new JsonResponse(['message' => 'Invalid Search Type'], 400);
                }
            }
        }

        $query = $this->vehicleRepository->findAllNotDeleted($this->vehicleType, $sort, $search);
        $vehicles = $this->getSinglePageVehicles($query, $page);

        $arrayCollection = array();

        foreach($vehicles as $vehicle) {
            $arrayCollection[] = $vehicle->toArray();
        }

        return new JsonResponse($arrayCollection,200);
    }

    /**
     * Route for getting data of single vehicle
     *
     * @Route("/vehicle/{id}", name="vehicle_show", methods={"GET"})
     */
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $vehicle = $this->vehicleRepository->find($id);

        if (!$vehicle) {
            throw $this->createNotFoundException(
                'No vehicle found for id '.$id
            );
        }

        return new JsonResponse($vehicle->toArray(), 200);
    }

    /**
     * Route for creating new vehicle
     *
     * @Route("/vehicle", name="vehicle_create", methods={"POST"})
     */
    public function create(Request $request, ManagerRegistry $doctrine,ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $entityManager = $doctrine->getManager();

        $vehicle = new Vehicle();

        $vehicle->setDateAdded(\DateTime::createFromFormat('Y-m-d H:i:s', $data['dateAdded']));
        $vehicle->setType($data['type'] ?? "");
        $vehicle->setMsrp($data['msrp'] ?? 0.0);
        $vehicle->setYear($data['year'] ?? 0);
        $vehicle->setMake($data['make'] ?? "");
        $vehicle->setModel($data['model'] ?? "");
        $vehicle->setMiles($data['miles'] ?? 0);
        $vehicle->setVin($data['vin'] ?? "");
        $vehicle->setDeleted($data['deleted'] ?? false);

        $errors = $validator->validate($vehicle);
        if (count($errors) > 0) {
            return new JsonResponse((string) $errors, 400);
        }

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($vehicle);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new JsonResponse('Saved new vehicle with id '.$vehicle->getId(), 200);
    }

    /**
     * Route for updating vehicle information
     *
     * @Route("/vehicle/{id}", name="vehicle_update", methods={"PATCH"})
     */
    public function update(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $entityManager = $doctrine->getManager();

        $vehicle = $doctrine->getRepository(Vehicle::class)->find($id);

        if (!$vehicle) {
            throw $this->createNotFoundException(
                'No vehicle found for id '.$id
            );
        }

        isset($data['dateAdded']) && $vehicle->setDateAdded(\DateTime::createFromFormat('Y-m-d H:i:s', $data['dateAdded']));
        isset($data['msrp']) && $vehicle->setMsrp($data['msrp']);
        isset($data['year']) && $vehicle->setYear($data['year']);
        isset($data['make']) && $vehicle->setMake($data['make']);
        isset($data['model']) && $vehicle->setModel($data['model']);
        isset($data['miles']) && $vehicle->setMiles($data['miles']);
        isset($data['vin']) && $vehicle->setVin($data['vin']);
        isset($data['deleted']) && $vehicle->setDeleted($data['deleted']);

        $errors = $validator->validate($vehicle);
        if (count($errors) > 0) {
            return new JsonResponse((string) $errors, 400);
        }

        $entityManager->persist($vehicle);

        $entityManager->flush();

        return new JsonResponse($vehicle->toArray(), 200);
    }

    /**
     * Route for deleting a vehicle. The delete is soft delete and
     * not actually deleted from database but the column 'deleted' is set to true;
     *
     * @Route("/vehicle/{id}", name="vehicle_delete", methods={"DELETE"})
     */
    public function delete(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $vehicle = $this->vehicleRepository->find($id);

        if (!$vehicle) {
            throw $this->createNotFoundException(
                'No vehicle found for id '.$id
            );
        }

        $vehicle->setDeleted(true);
        $entityManager->flush();

        return new JsonResponse('Vehicle Successfully Deleted', 202);
    }

    /**
     * @Route("/vehicles/{page}", name="vehicle_list", methods={"GET"}, requirements={"page"="\d+"})
     */
    public function getSinglePageVehicles($query, $page): Paginator
    {
        //set page size
        $pageSize = '10';

        // load doctrine Paginator
        $paginator = new Paginator($query);

        // you can get total items
        $totalItems = count($paginator);

        // get total pages
        $pagesCount = ceil($totalItems / $pageSize);

        // now get one page's items:
        $paginator
            ->getQuery()
            ->setFirstResult($pageSize * ($page-1)) // set the offset
            ->setMaxResults($pageSize); // set the limit

        return $paginator;
    }
}
