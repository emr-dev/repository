# Repository.php
The "Repository" is a helper for php which helps to quickly and conveniently generate and execute certain sql queries to the application database.
## How to use it
Repository.php is easy to use, as you only need to connect one single file to the application and enjoy the process =)

1. Include Repository.php
```
include 'Repository.php';
```
2. Define authorization variables in your project
```
define('REPOSITORY_PDO','mysql');
define('REPOSITORY_HOST','***');
define('REPOSITORY_USER','***');
define('REPOSITORY_PASSWORD','***');
define('REPOSITORY_DBNAME','***');
define('REPOSITORY_CHARSET','utf8');
```
3. And the most important thing
```
enjoy =)
```
## Examples of using
```
$repository = new Repository(); 
```

- Get Item by ID
  ```
  $repository->getManager('table')->find($id);
  ```

- Get Items by Filter
  ```
  $repository->getManager('table')->findby(['name'=>'value']);
  ```

- Get Items by Filter with limit and start
  ```
  $repository->getManager('table')->findby(['name'=>'value'], $start, $limit);
  ```  

- Get one item by Filter
  ```
  $repository->getManager('table')->findOneBy(['name'=>'value']);  

- Add a note
  ```
  $repository->getManager('table')->insert(['name'=>'value',...]);
    
- Remove a note
  ```
  $repository->getManager('table')->remove($id);

- Update a note
  ```
  $repository->getManager('table')->update(['name'=>'value',...], $id);
