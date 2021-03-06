# Instructions

Clone the Repo and checkout the `master` branch.<br/>
Create a `.env` file and copy the content of `.env.example` file. Update database congif in `.env` file.<br/>
There is a parameter VEHICLE_TYPE in the `.env` file. Based on the that value the GET index request filters the vehicle from the databse.<br/>
Default value is set to used. It can only have ```new|used``` value <br/>

In the directory run the following commands<br/>

1. `composer update`
2. `php bin/console doctrine:database:create`
3. `php bin/console make:migration`
4. `php bin/console doctrine:migrations:migrate`
5. `php bin/console doctrine:fixtures:load`
6. `symfony server:start`

The server should start at http://127.0.0.1:someport<br/>

#**Following Restful APIs are available**

##1. Method=GET, route= `/vehicles`
List all the vehicles that are not deleted and vehicle type based on the evironment variable;
Pagination, Sorting and Filtering is accomplished using query parameters as shown in the example requests below.
Sample: URLs
`http://127.0.0.1:8001/vehicles`
`http://127.0.0.1:8001/vehicles?page=1&&sort=make`
`http://127.0.0.1:8001/vehicles?page=1&&search[make]=Toyota`

##2. Method = GET, route= `/vehicle/{id}`
Returns the vehicle information based on the id. Vehicle type and deleted column is not considered while fetching from database

URL:`http://127.0.0.1:8001/vehicle/1`

##3. Method = POST, route= `/vehicle`
Create a vehicle entry in the database.
URL: `http://127.0.0.1:8001/vehicle`
Request Body:

```
{
    "dateAdded":"2022-01-01 12:01:15",
    "type":"used",
    "msrp":"1",
    "year":"1993",
    "make":"Toyota",
    "model":"Corolla",
    "miles":"153000",
    "vin":"484848848848493"
}
```

##4. Method = PATCH, route= `/vehicle/{id}`<br/>
Update a vehicle entry in the database.<br/>
URL:`http://127.0.0.1:8001/vehicle/1`<br/>
Request Body:

```
{
    "dateAdded":"2022-01-01 12:01:15",
    "type":"used",
    "msrp":"1",
    "year":"1993",
    "make":"Toyota",
    "model":"Corolla",
    "miles":"153000",
    "vin":"484848848848493"
}
```

##5. Method = DELETE, route= `/vehicle/{id}`<br/>
Delete a vehicle entry in the database. This url only does soft delete and updates deleted column in the database.<br/>
URL:`http://127.0.0.1:8001/vehicle/1`<br/>

Swagger API is implemented and can be accessed using following route: http://127.0.0.1:8000/api<br/>
