<!DOCTYPE html>
<html lang="en">
<?php require_once '../auth.php'; ?>
<?php
require __DIR__ . '/../vendor/autoload.php';

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\TwitterBootstrap4View;

?>


<head>

    <?php include 'globalhead.php'; ?>

</head>

<body id="colorbox" class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
<?php require_once("navbar.php"); ?>
<br /><br /><br /><br />
<?php if (isLoggedIn()) { ?>
    <?php if(isSidebar()==0){ ?>
      <div class="container-fluid" style="width:80%;margin-left:16%;">
    <?php }else if(isSidebar()==1){ ?>
        <div class="container-fluid" style="width:90%; margin: 0 auto;">
        <?php } ?>
    <?php } else { ?>

      <div class="container-fluid" style="margin-left:auto;margin-right:auto;">

      <?php } ?>

        <?php
        require_once("../dbconnect.php");

        $sql = "SELECT 
    [ProgramName],
    [ArchivePacketID],
    [SheetName],
    [MachineName],
    [ActualStartTime],
    [ActualEndTime],
    [ActualState],
    CASE WHEN CHARINDEX(',', [Comment]) > 0 THEN
        SUBSTRING([Comment], 1, CHARINDEX(',', [Comment]) - 1)
    ELSE
        [Comment]
    END AS CommentBeforeComma,
    CASE WHEN CHARINDEX(',', [Comment]) > 0 THEN
        SUBSTRING([Comment], CHARINDEX(',', [Comment]) + 1, LEN([Comment]))
    ELSE
        NULL  -- or any default value you want when there is no comma
    END AS CommentAfterComma,
    CONVERT(CHAR(8), DATEADD(second, [CuttingTime], 0), 108) AS czaspalenia
FROM 
    [SNDBASE_PROD].[dbo].[ProgArchive]
where [Comment]!=''
ORDER BY 
    CommentAfterComma DESC;";
        $datas = sqlsrv_query($conn, $sql);
        $dataresult = [];

        while ($row = sqlsrv_fetch_array($datas, SQLSRV_FETCH_ASSOC)) {
            $dataresult[] = $row;
        }
        $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';
        $keywordArray = explode(' ', $keywords);
        $filteredData = array_filter($dataresult, function ($item) use ($keywordArray) {
            if ($keywordArray !== '') {
                foreach ($keywordArray as $keyword) :
                    $keyword = trim($keyword);
                $columnsToSearch = ['CommentAfterComma','CommentBeforeComma','ProgramName', 'MachineName']; // Dodaj więcej kolumn, jeśli jest potrzebne
                    $matchesKeyword = false;
                    foreach ($columnsToSearch as $column) {
                        $columnValue = $item[$column] instanceof DateTime ? $item[$column]->format('Y-m-d H:i:s') : $item[$column];
                        if (stripos($columnValue, $keyword) !== false) {
                            $matchesKeyword = true;
                            break;
                        }
                    }
                    if (!$matchesKeyword) {
                        return false;
                    }
                
            endforeach;
            }
            return true;
        });
        $pageSize = 50;
        $pageNumber = isset($_GET['page']) ? $_GET['page'] : 1;
        $showAll = $pageSize == count($filteredData); // Sprawdzamy, czy wartość jest równa -1, aby określić, czy "ALL" jest wybrane

        if ($showAll) {
            $pageSize = count($filteredData);
        } else {
            $pageSize = (int)$pageSize;
            $pageSize = max(1, $pageSize); // Upewniamy się, że $pageSize jest większe lub równe 1
        }
        $adapter = new ArrayAdapter($filteredData);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($pageSize);
        $pagerfanta->setCurrentPage($pageNumber);

        $currentPageResults = $pagerfanta->getCurrentPageResults();


        ?>
        <div class="mb-3" style="float:right;">

            <form id="myForm1" method="get" action="">
                    <input type="text" class="form-control" name="keywords" style="float:left;width:65%" placeholder="<?php echo $keywords; ?>"
                           placeholder="Nazwa..." autofocus>

                    <button class="btn btn-primary" style="float:left;width:30%" type="submit">Szukaj</button>
                    <br/>
        </div>
        </form>

        <div style="clear:both;"></div>
        <div class="table-responsive">
            <table class="table table-xl table-hover table-striped" id="mytable"
                   style="font-size: calc(9px + 0.390625vw)">
                <thead>
                <th>Osoba/powód</th>
                <th>Nazwa programu</th>
                <th>Nazwa arkusza</th>
                <th>maszyna</th>
                <th>czas</th>
                <th>data i czas</th>
                </thead>
                <tbody class="row_position">
                <?php foreach ($currentPageResults as $data) : ?>
                    <tr id="<?php echo $data['ArchivePacketID']; ?>">
                        <td>
                            <?php echo isset($data['CommentBeforeComma']) ? $data['CommentBeforeComma'] : ''; ?>
                        </td>
                        <td>
                            <?php echo isset($data['ProgramName']) ? $data['ProgramName'] : ''; ?>
                        </td>
                        <td>
                            <?php echo isset($data['SheetName']) ? $data['SheetName'] : ''; ?>
                        </td>
                        <td>
                            <?php echo isset($data['MachineName']) ? $data['MachineName'] : ''; ?>
                        </td>
                        <td>
                            <?php echo isset($data['czaspalenia']) ? $data['czaspalenia'] : ''; ?>
                        </td>
                        <td>
                            <?php echo isset($data['CommentAfterComma']) && $data['CommentAfterComma'] !== '' ? $data['CommentAfterComma'] : ''; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div style="float:right">
        <?php

        $view = new TwitterBootstrap4View();
        $options = array(
          'prev_message' => '<',
          'next_message' => '>',
          'routeGenerator' => function ($page) {
            $queryString = $_SERVER['QUERY_STRING'];
            parse_str($queryString, $queryParams);
            $queryParams['page'] = $page;
            $newQueryString = http_build_query($queryParams);
            $url = $_SERVER['PHP_SELF'] . '?' . $newQueryString;
            return $url;
          },
        );

        echo $view->render($pagerfanta, $options['routeGenerator'], $options);
        ?>

      </div>
        </div>
    </div>
</div>
<?php include 'globalnav.php'; ?>
</body>
<script>
 var currentPage = <?php echo isset($_GET['page']) ? $_GET['page'] : 1; ?>;

  const pageItems = document.querySelectorAll('.pagination li');

  // Iteracja przez każdy element li i usunięcie słowa "Current"
  pageItems.forEach(function(item) {
      if (item.classList.contains('active')) {
          item.querySelector('.page-link').innerHTML = currentPage;
      }
  });
</script>
</html>