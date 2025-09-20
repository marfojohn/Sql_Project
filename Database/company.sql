-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 20, 2025 at 07:34 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `company`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `answer_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `query_text` text NOT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branch`
--

CREATE TABLE `branch` (
  `branch_id` int(11) NOT NULL,
  `branch_name` varchar(40) DEFAULT NULL,
  `mgr_id` int(11) DEFAULT NULL,
  `mgr_start_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branch`
--

INSERT INTO `branch` (`branch_id`, `branch_name`, `mgr_id`, `mgr_start_date`) VALUES
(1, 'Corporate', 100, '2006-02-09'),
(2, 'Scranton', 102, '1992-04-06'),
(3, 'Stamford', 106, '1998-02-13'),
(4, 'Buffaloo', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `branch_supplier`
--

CREATE TABLE `branch_supplier` (
  `branch_id` int(11) NOT NULL,
  `supplier_name` varchar(40) NOT NULL,
  `supply_type` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branch_supplier`
--

INSERT INTO `branch_supplier` (`branch_id`, `supplier_name`, `supply_type`) VALUES
(2, 'Hammer Mill', 'Paper'),
(2, 'J.T Forms & Labels', 'Custom Forms'),
(2, 'Uni-ball', 'Writing Utensils'),
(3, 'Hammer Mill', 'Paper'),
(3, 'Patriot Paper', 'Paper'),
(3, 'Stamford Lables', 'Custom Forms'),
(3, 'Uni-ball', 'Writing Utensils');

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `client_id` int(11) NOT NULL,
  `client_name` varchar(40) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`client_id`, `client_name`, `branch_id`) VALUES
(400, 'Dunmore Highschool', 2),
(401, 'Lackawana County', 2),
(402, 'FedEX', 3),
(403, 'John Daly Law, LLC', 3),
(404, 'Scranton Whitepages', 2),
(405, 'Times Newspaper', 3),
(406, 'FedEx', 2);

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `emp_id` int(11) NOT NULL,
  `first_name` varchar(40) DEFAULT NULL,
  `last_name` varchar(40) DEFAULT NULL,
  `birth_day` date DEFAULT NULL,
  `sex` varchar(1) DEFAULT NULL,
  `salary` int(11) DEFAULT NULL,
  `super_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`emp_id`, `first_name`, `last_name`, `birth_day`, `sex`, `salary`, `super_id`, `branch_id`) VALUES
(100, 'David', 'Wallace', '1967-11-17', 'M', 25000, NULL, 1),
(101, 'Jan', 'Levinson', '1961-05-11', 'F', 110000, 100, 1),
(102, 'Micheal', 'Scott', '1964-03-15', 'M', 75000, 100, 2),
(103, 'Angela', 'Martin', '1971-06-25', 'F', 63000, 102, 2),
(104, 'Kelly', 'Kapoor', '1980-02-25', 'F', 55000, 102, 2),
(105, 'Stanley', 'Hudson', '1958-02-19', 'M', 69000, 102, 2),
(106, 'Josh', 'Porter', '1969-09-05', 'M', 78000, 100, 3),
(107, 'Andy', 'Bernard', '1973-07-22', 'M', 65000, 106, 3),
(108, 'Jim', 'Halpert', '1978-10-01', 'M', 71000, 106, 3);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `question_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `correct_query` text DEFAULT NULL,
  `expected_results` longtext DEFAULT NULL,
  `difficulty` enum('basic','intermediate','join','advanced') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`question_id`, `question_text`, `correct_query`, `expected_results`, `difficulty`) VALUES
(1, 'Select all records from the Employee table.', 'SELECT * FROM Employee;', '[{\"emp_id\":100,\"first_name\":\"David\",\"last_name\":\"Wallace\",\"birth_day\":\"1967-11-17\",\"sex\":\"M\",\"salary\":25000,\"super_id\":null,\"branch_id\":1},\r\n    {\"emp_id\":101,\"first_name\":\"Jan\",\"last_name\":\"Levinson\",\"birth_day\":\"1961-05-11\",\"sex\":\"F\",\"salary\":110000,\"super_id\":100,\"branch_id\":1}]', 'basic'),
(2, 'Get the birth_date of employee with emp_id = 103.\r\n', 'SELECT birth_date FROM Employee WHERE emp_id = 103;\r\n', NULL, 'basic'),
(3, 'Display all branch names', 'SELECT branch_name FROM Branch;\r\n', NULL, 'basic'),
(4, 'Display first_name and last_name of all employees.', 'SELECT first_name, last_name FROM Employee;', '[{\"first_name\":\"David\",\"last_name\":\"Wallace\"},\r\n    {\"first_name\":\"Jan\",\"last_name\":\"Levinson\"},\r\n    {\"first_name\":\"Micheal\",\"last_name\":\"Scott\"},\r\n    {\"first_name\":\"Angela\",\"last_name\":\"Martin\"},\r\n    {\"first_name\":\"Kelly\",\"last_name\":\"Kapoor\"},\r\n    {\"first_name\":\"Stanley\",\"last_name\":\"Hudson\"},\r\n    {\"first_name\":\"Josh\",\"last_name\":\"Porter\"},\r\n    {\"first_name\":\"Andy\",\"last_name\":\"Bernard\"},\r\n    {\"first_name\":\"Jim\",\"last_name\":\"Halpert\"}]', 'basic'),
(5, 'Show all employees who are female.', 'SELECT * FROM Employee WHERE sex = \'F\';', '[{\"emp_id\":101,\"first_name\":\"Jan\",\"last_name\":\"Levinson\",\"birth_day\":\"1961-05-11\",\"sex\":\"F\",\"salary\":110000,\"super_id\":100,\"branch_id\":1},\r\n    {\"emp_id\":103,\"first_name\":\"Angela\",\"last_name\":\"Martin\",\"birth_day\":\"1971-06-25\",\"sex\":\"F\",\"salary\":63000,\"super_id\":102,\"branch_id\":2},\r\n    {\"emp_id\":104,\"first_name\":\"Kelly\",\"last_name\":\"Kapoor\",\"birth_day\":\"1980-02-25\",\"sex\":\"F\",\"salary\":55000,\"super_id\":102,\"branch_id\":2}]', 'basic'),
(6, 'Show all employees in branch_id = 2.', 'SELECT * FROM Employee WHERE branch_id = 2;', NULL, 'basic'),
(7, 'Retrieve all suppliers and their supply types.', 'SELECT supplier_name, supply_type FROM Branch_Supplier;', NULL, 'basic'),
(8, 'List all client names.', 'SELECT client_name FROM Client;', NULL, 'basic'),
(9, 'Show the salary of all employees in ascending order.', 'SELECT salary FROM Employee ORDER BY salary ASC;', NULL, 'basic'),
(10, 'Get details of employee \"Michael Scott\".', 'SELECT * FROM Employee WHERE emp_id = 102;', NULL, 'basic'),
(11, 'Show the branch where \"Jim Halpert\" works.', 'SELECT branch_id FROM Employee WHERE first_name = \'Jim\' AND last_name = \'Halpert\';', NULL, 'basic'),
(12, 'Display all employees with salary greater than 100,000.', 'SELECT * FROM Employee WHERE salary > 100000;', NULL, 'basic'),
(13, 'Show all clients handled by branch 3.', 'SELECT * FROM Client WHERE branch_id = 3;', NULL, 'basic'),
(14, 'Display names of employees born after 1970.', 'SELECT first_name, last_name FROM Employee WHERE birth_day > \'1970-01-01\';', NULL, 'basic'),
(15, 'Retrieve all suppliers that provide paper.', 'SELECT * FROM Branch_Supplier WHERE supply_type = \'Paper\';', NULL, 'basic'),
(16, 'Find all male employees.', 'SELECT * FROM Employee WHERE sex = \'M\';', NULL, 'basic'),
(17, 'Show the total number of employees in each branch.', 'SELECT branch_id, COUNT(*) AS total_employees FROM Employee GROUP BY branch_id;', NULL, 'intermediate'),
(18, 'Get details of the employee with the highest salary.', 'SELECT * FROM Employee WHERE salary = (SELECT MAX(salary) FROM Employee);', NULL, 'intermediate'),
(19, 'Retrieve all employees who don’t have a supervisor.', 'SELECT * FROM Employee WHERE super_id IS NULL;', NULL, 'basic'),
(20, 'List all unique supply types.', 'SELECT DISTINCT supply_type FROM Branch_Supplier;', NULL, 'basic'),
(21, 'Show employees with salary between 60,000 and 100,000.', 'SELECT * FROM Employee WHERE salary BETWEEN 60000 AND 100000;', NULL, 'basic'),
(22, 'Get employees whose last name starts with \"H\".', 'SELECT * FROM Employee WHERE last_name LIKE \'H%\';', NULL, 'basic'),
(23, 'Find employees born before 1965.', 'SELECT * FROM Employee WHERE birth_day < \'1965-01-01\';', NULL, 'basic'),
(24, 'List employees who earn less than 80,000.', 'SELECT * FROM Employee WHERE salary < 80000;', NULL, 'basic'),
(25, 'Display clients whose name contains \"FedEx\".', 'SELECT * FROM Client WHERE client_name LIKE \'%FedEx%\';', NULL, 'basic'),
(26, 'Show employees whose first name ends with \"n\".', 'SELECT * FROM Employee WHERE first_name LIKE \'%n\';', NULL, 'basic'),
(27, 'Find all suppliers whose name contains \"Uni\".', 'SELECT * FROM Branch_Supplier WHERE supplier_name LIKE \'%Uni%\';', NULL, 'basic'),
(28, 'Show employees with super_id = 102.', 'SELECT * FROM Employee WHERE super_id = 102;', NULL, 'basic'),
(29, 'Display employees working in branch_id = 1 or 2.', 'SELECT * FROM Employee WHERE branch_id IN (1, 2);', NULL, 'basic'),
(30, 'List all employees not in branch 3.', 'SELECT * FROM Employee WHERE branch_id <> 3;', NULL, 'basic'),
(31, 'Show employees with salary not equal to 71,000.', 'SELECT * FROM Employee WHERE salary <> 71000;', NULL, 'basic'),
(32, 'Find employees born in September.', 'SELECT * FROM Employee WHERE MONTH(birth_day) = 9;', NULL, 'intermediate'),
(33, 'Display clients whose client_id is greater than 402.', 'SELECT * FROM Client WHERE client_id > 402;', NULL, 'basic'),
(34, 'Show employees with salary divisible by 5,000.', 'SELECT * FROM Employee WHERE salary % 5000 = 0;', NULL, 'basic'),
(35, 'Display employees whose last_name is \"Hudson\".', 'SELECT * FROM Employee WHERE last_name = \'Hudson\';', NULL, 'basic'),
(36, 'Find clients whose name length is greater than 10 characters.', 'SELECT * FROM Client WHERE LENGTH(client_name) > 10;', NULL, 'intermediate'),
(37, 'Show all employees whose salary is greater than their supervisor’s salary.', 'SELECT e.emp_id, e.first_name, e.last_name, e.salary, s.salary AS supervisor_salary \r\nFROM Employee e \r\nJOIN Employee s ON e.super_id = s.emp_id \r\nWHERE e.salary > s.salary;', NULL, 'intermediate'),
(38, 'List employees with salary equal to branch manager’s salary.', 'SELECT e.* \r\nFROM Employee e \r\nJOIN Branch b ON e.branch_id = b.branch_id \r\nJOIN Employee m ON b.mgr_id = m.emp_id \r\nWHERE e.salary = m.salary;', NULL, 'intermediate'),
(39, 'Display employees with birth date in the 1960s.', 'SELECT * FROM Employee WHERE YEAR(birth_day) BETWEEN 1960 AND 1969;', NULL, 'basic'),
(40, 'Show clients with branch_id not equal to 2.', 'SELECT * FROM Client WHERE branch_id <> 2;', NULL, 'basic'),
(41, 'Count total number of employees.', 'SELECT COUNT(*) AS total_employees FROM Employee;', NULL, 'basic'),
(42, 'Find the average salary of employees.', 'SELECT AVG(salary) AS avg_salary FROM Employee;', NULL, 'basic'),
(43, 'Get the maximum salary.', 'SELECT MAX(salary) AS max_salary FROM Employee;', NULL, 'basic'),
(44, 'Get the minimum salary.', 'SELECT MIN(salary) AS min_salary FROM Employee;', NULL, 'basic'),
(45, 'Find the total salary expense for branch_id = 3.', 'SELECT SUM(salary) AS total_salary_expense FROM Employee WHERE branch_id = 3;', NULL, 'basic'),
(46, 'Count how many female employees exist.', 'SELECT COUNT(*) AS female_count FROM Employee WHERE sex = \'F\';', NULL, 'basic'),
(47, 'Find the average salary of male employees.', 'SELECT AVG(salary) AS avg_male_salary FROM Employee WHERE sex = \'M\';', NULL, 'basic'),
(48, 'Count total number of clients in each branch.', 'SELECT branch_id, COUNT(*) AS client_count FROM Client GROUP BY branch_id;', NULL, 'intermediate'),
(49, 'Get the highest total_sales value.', 'SELECT MAX(total_sales) AS max_sales FROM Works_With;', NULL, 'basic'),
(50, 'Calculate the sum of all sales and name it total_sales_sum', 'SELECT SUM(total_sales) AS total_sales_sum FROM Works_With;', NULL, 'basic'),
(51, 'Find the average total_sales per employee.', 'SELECT emp_id, AVG(total_sales) AS avg_sales FROM Works_With GROUP BY emp_id;', NULL, 'intermediate'),
(52, 'Count how many suppliers each branch has.', 'SELECT branch_id, COUNT(*) AS supplier_count FROM Branch_Supplier GROUP BY branch_id;', NULL, 'intermediate'),
(53, 'Find the supplier count for branch_id = 2.', 'SELECT COUNT(*) AS supplier_count FROM Branch_Supplier WHERE branch_id = 2;', NULL, 'basic'),
(54, 'Get the number of employees supervised by emp_id = 100 and query your rsult column back as subordinates', 'SELECT COUNT(*) AS subordinates FROM Employee WHERE super_id = 100;', NULL, 'basic'),
(55, 'Find total sales handled by employee 105.', 'SELECT SUM(total_sales) AS total_sales_105 FROM Works_With WHERE emp_id = 105;', NULL, 'basic'),
(56, 'Show total sales for each client.', 'SELECT client_id, SUM(total_sales) AS client_sales FROM Works_With GROUP BY client_id;', NULL, 'intermediate'),
(57, 'Show the branch with the maximum number of clients.', 'SELECT branch_id, COUNT(*) AS client_count FROM Client GROUP BY branch_id ORDER BY client_count DESC LIMIT 1;', NULL, 'intermediate'),
(58, 'Find the employee with the highest total sales.', 'SELECT emp_id, SUM(total_sales) AS total_sales FROM Works_With GROUP BY emp_id ORDER BY total_sales DESC LIMIT 1;', NULL, 'intermediate'),
(59, 'Show the average salary of employees in each branch.', 'SELECT branch_id, AVG(salary) AS avg_salary FROM Employee GROUP BY branch_id;', NULL, 'intermediate'),
(60, 'Count the number of supervisors (employees referenced in super_id).', 'SELECT COUNT(DISTINCT super_id) AS supervisors_count FROM Employee WHERE super_id IS NOT NULL;', NULL, 'intermediate'),
(61, 'Display employee names with their branch name.', 'SELECT e.first_name, e.last_name, b.branch_name FROM Employee e JOIN Branch b ON e.branch_id = b.branch_id;', NULL, 'intermediate'),
(62, 'Show branch manager names with branch name.', 'SELECT b.branch_name, e.first_name, e.last_name FROM Branch b JOIN Employee e ON b.mgr_id = e.emp_id;', NULL, 'intermediate'),
(63, 'List client names along with their branch name.', 'SELECT c.client_name, b.branch_name FROM Client c JOIN Branch b ON c.branch_id = b.branch_id;', NULL, 'intermediate'),
(64, 'Show employees and the clients they worked with.', 'SELECT e.first_name, e.last_name, c.client_name, w.total_sales FROM Works_With w JOIN Employee e ON w.emp_id = e.emp_id JOIN Client c ON w.client_id = c.client_id;', NULL, 'advanced'),
(65, 'Display employee names with their supervisor names.', 'SELECT e.first_name AS employee_first, e.last_name AS employee_last, s.first_name AS supervisor_first, s.last_name AS supervisor_last FROM Employee e JOIN Employee s ON e.super_id = s.emp_id;', NULL, 'advanced'),
(66, 'Show employee names with total sales they generated.', 'SELECT e.first_name, e.last_name, SUM(w.total_sales) AS total_sales FROM Employee e JOIN Works_With w ON e.emp_id = w.emp_id GROUP BY e.emp_id, e.first_name, e.last_name;', NULL, 'advanced'),
(67, 'Show all clients with the employees who worked with them.', 'SELECT c.client_name, e.first_name, e.last_name FROM Works_With w JOIN Client c ON w.client_id = c.client_id JOIN Employee e ON w.emp_id = e.emp_id;', NULL, 'advanced'),
(68, 'List all suppliers with their corresponding branch names.', 'SELECT bs.supplier_name, bs.supply_type, b.branch_name FROM Branch_Supplier bs JOIN Branch b ON bs.branch_id = b.branch_id;', NULL, 'intermediate'),
(69, 'Show employees who belong to Stamford branch.', 'SELECT e.* FROM Employee e JOIN Branch b ON e.branch_id = b.branch_id WHERE b.branch_name = \'Stamford\';', NULL, 'intermediate'),
(70, 'Display the names of employees who manage a branch.', 'SELECT e.first_name, e.last_name, b.branch_name FROM Branch b JOIN Employee e ON b.mgr_id = e.emp_id;', NULL, 'intermediate'),
(71, 'Find employees working for the same branch as David Wallace.', 'SELECT e.first_name, e.last_name FROM Employee e WHERE e.branch_id = (SELECT branch_id FROM Employee WHERE first_name = \'David\' AND last_name = \'Wallace\') AND NOT (e.first_name = \'David\' AND e.last_name = \'Wallace\');', NULL, 'advanced'),
(72, 'Show client names and their assigned branch manager.', 'SELECT c.client_name, m.first_name, m.last_name AS manager_name FROM Client c JOIN Branch b ON c.branch_id = b.branch_id JOIN Employee m ON b.mgr_id = m.emp_id;', NULL, 'advanced'),
(73, 'List employees who work in branches supplied by Hammer Mill.', 'SELECT DISTINCT e.first_name, e.last_name FROM Employee e JOIN Branch_Supplier bs ON e.branch_id = bs.branch_id WHERE bs.supplier_name = \'Hammer Mill\';', NULL, 'advanced'),
(74, 'Display employees along with their branch’s supplier names.', 'SELECT e.first_name, e.last_name, bs.supplier_name FROM Employee e JOIN Branch_Supplier bs ON e.branch_id = bs.branch_id;', NULL, 'advanced'),
(75, 'Show employees who sold to FedEx.', 'SELECT DISTINCT e.first_name, e.last_name FROM Works_With w JOIN Employee e ON w.emp_id = e.emp_id JOIN Client c ON w.client_id = c.client_id WHERE c.client_name = \'FedEx\';', NULL, 'advanced'),
(76, 'Display total sales grouped by branch name.', 'SELECT b.branch_name, SUM(w.total_sales) AS total_sales FROM Works_With w JOIN Employee e ON w.emp_id = e.emp_id JOIN Branch b ON e.branch_id = b.branch_id GROUP BY b.branch_name;', NULL, 'advanced'),
(77, 'Show all supervisors along with their subordinates.', 'SELECT s.first_name AS supervisor_first, s.last_name AS supervisor_last, e.first_name AS subordinate_first, e.last_name AS subordinate_last FROM Employee e JOIN Employee s ON e.super_id = s.emp_id;', NULL, 'advanced'),
(78, 'Display each branch manager and their start date.', 'SELECT e.first_name, e.last_name, b.branch_name, b.mgr_start_date FROM Branch b JOIN Employee e ON b.mgr_id = e.emp_id;', NULL, 'intermediate'),
(79, 'Show client names along with all employees who made sales to them.', 'SELECT c.client_name, e.first_name, e.last_name FROM Works_With w JOIN Client c ON w.client_id = c.client_id JOIN Employee e ON w.emp_id = e.emp_id;', NULL, 'advanced'),
(80, 'Display suppliers that provide supplies to branches handling FedEx.', 'SELECT DISTINCT bs.supplier_name, bs.supply_type FROM Branch_Supplier bs JOIN Branch b ON bs.branch_id = b.branch_id JOIN Client c ON b.branch_id = c.branch_id WHERE c.client_name = \'FedEx\';', NULL, 'advanced'),
(81, 'Find employees earning more than the average salary.', 'SELECT * FROM Employee WHERE salary > (SELECT AVG(salary) FROM Employee);', NULL, 'advanced'),
(82, 'Show employees who earn above their branch average.', 'SELECT e.* FROM Employee e JOIN (SELECT branch_id, AVG(salary) AS branch_avg FROM Employee GROUP BY branch_id) b ON e.branch_id = b.branch_id WHERE e.salary > b.branch_avg;', NULL, 'advanced'),
(83, 'Find employees older than their supervisor.', 'SELECT e.first_name, e.last_name, e.birth_date, s.first_name AS supervisor_first, s.birth_date AS supervisor_birth FROM Employee e JOIN Employee s ON e.super_id = s.emp_id WHERE e.birth_date < s.birth_date;', NULL, 'advanced'),
(84, 'Display branch managers who manage employees older than themselves.', 'SELECT DISTINCT m.first_name, m.last_name, b.branch_name FROM Branch b JOIN Employee m ON b.mgr_id = m.emp_id JOIN Employee e ON e.branch_id = b.branch_id WHERE e.birth_date < m.birth_date;', NULL, 'advanced'),
(85, 'Show clients who have been worked with by more than 2 employees.', 'SELECT c.client_name, COUNT(DISTINCT w.emp_id) AS num_employees FROM Works_With w JOIN Client c ON w.client_id = c.client_id GROUP BY c.client_id, c.client_name HAVING COUNT(DISTINCT w.emp_id) > 2;', NULL, 'advanced'),
(86, 'Find branches with more than 2 employees.', 'SELECT branch_id, COUNT(*) AS employee_count FROM Employee GROUP BY branch_id HAVING COUNT(*) > 2;', NULL, 'intermediate'),
(87, 'Display employees who made sales greater than 100,000.', 'SELECT DISTINCT e.first_name, e.last_name FROM Works_With w JOIN Employee e ON w.emp_id = e.emp_id WHERE w.total_sales > 100000;', NULL, 'intermediate'),
(88, 'Find clients who have sales totaling above 150,000.', 'SELECT c.client_name, SUM(w.total_sales) AS total_sales FROM Works_With w JOIN Client c ON w.client_id = c.client_id GROUP BY c.client_name HAVING SUM(w.total_sales) > 150000;', NULL, 'advanced'),
(89, 'Show employees who worked with at least 2 different clients.', 'SELECT e.first_name, e.last_name, COUNT(DISTINCT w.client_id) AS clients_count FROM Works_With w JOIN Employee e ON w.emp_id = e.emp_id GROUP BY e.emp_id, e.first_name, e.last_name HAVING COUNT(DISTINCT w.client_id) >= 2;', NULL, 'advanced'),
(90, 'List supervisors who manage more than 1 employee.', 'SELECT s.first_name, s.last_name, COUNT(e.emp_id) AS subordinates FROM Employee e JOIN Employee s ON e.super_id = s.emp_id GROUP BY s.emp_id, s.first_name, s.last_name HAVING COUNT(e.emp_id) > 1;', NULL, 'intermediate'),
(91, 'Find suppliers providing more than one supply type.', 'SELECT supplier_name, COUNT(DISTINCT supply_type) AS supply_types FROM Branch_Supplier GROUP BY supplier_name HAVING COUNT(DISTINCT supply_type) > 1;', NULL, 'advanced'),
(92, 'Display branches with no suppliers.', 'SELECT b.branch_id, b.branch_name FROM Branch b LEFT JOIN Branch_Supplier bs ON b.branch_id = bs.branch_id WHERE bs.branch_id IS NULL;', NULL, 'advanced'),
(93, 'Show employees who don’t supervise anyone.', 'SELECT e.emp_id, e.first_name, e.last_name FROM Employee e LEFT JOIN Employee sub ON e.emp_id = sub.super_id WHERE sub.emp_id IS NULL;', NULL, 'intermediate'),
(94, 'Display employees who don’t work with any client.', 'SELECT e.emp_id, e.first_name, e.last_name FROM Employee e LEFT JOIN Works_With w ON e.emp_id = w.emp_id WHERE w.emp_id IS NULL;', NULL, 'intermediate'),
(95, 'Find clients not handled by branch_id = 2.', 'SELECT c.* FROM Client c WHERE c.branch_id <> 2;', NULL, 'basic'),
(96, 'Show employees who share the same salary.', 'SELECT e1.emp_id, e1.first_name, e1.last_name, e1.salary FROM Employee e1 JOIN Employee e2 ON e1.salary = e2.salary AND e1.emp_id <> e2.emp_id ORDER BY e1.salary;', NULL, 'advanced'),
(97, 'Find the youngest employee.', 'SELECT * FROM Employee ORDER BY birth_date DESC LIMIT 1;', NULL, 'basic'),
(98, 'Show employees who were born in the same year.', 'SELECT YEAR(birth_date) AS birth_year, GROUP_CONCAT(first_name, \' \', last_name) AS employees FROM Employee GROUP BY YEAR(birth_date) HAVING COUNT(*) > 1;', NULL, 'advanced'),
(99, 'Find the branch with the earliest manager start date.', 'SELECT branch_name, mgr_start_date FROM Branch ORDER BY mgr_start_date ASC LIMIT 1;', NULL, 'intermediate'),
(100, 'Show all employees whose salary is higher than the maximum salary in branch 2.', 'SELECT * FROM Employee WHERE salary > (SELECT MAX(salary) FROM Employee WHERE branch_id = 2);', NULL, 'advanced');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_answers`
--

CREATE TABLE `quiz_answers` (
  `answer_id` int(11) NOT NULL,
  `attempt_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `user_answer` text DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_answers`
--

INSERT INTO `quiz_answers` (`answer_id`, `attempt_id`, `question_id`, `user_answer`, `is_correct`) VALUES
(1, 1, 75, '', 0),
(2, 1, 25, '', 0),
(3, 1, 94, '', 0),
(4, 1, 91, '', 0),
(5, 1, 27, '', 0),
(6, 1, 71, '', 0),
(7, 1, 31, '', 0),
(8, 1, 22, '', 0),
(9, 1, 36, '', 0),
(10, 1, 62, '', 0),
(11, 2, 91, '', 0),
(12, 2, 47, '', 0),
(13, 2, 84, '', 0),
(14, 2, 51, '', 0),
(15, 2, 38, '', 0),
(16, 2, 13, '', 0),
(17, 2, 60, '', 0),
(18, 2, 28, '', 0),
(19, 2, 33, '', 0),
(20, 2, 80, '', 0),
(21, 3, 74, 'SELECT * FROM employee WHERE \n sex = \'M\';', 0),
(22, 3, 8, 'SELECT * FROM employee WHERE \nsalary <> 71000', 0),
(23, 3, 20, '', 0),
(24, 3, 98, '', 0),
(25, 3, 36, '', 0),
(26, 3, 18, '', 0),
(27, 3, 89, '', 0),
(28, 3, 52, '', 0),
(29, 3, 12, '', 0),
(30, 3, 7, '', 0),
(31, 14, 14, 'SELECT * FROM employee WHERE \n sex = \'M\';', 0),
(32, 14, 98, 'SELECT * FROM employee WHERE \nsalary <> 71000', 0),
(33, 14, 88, '', 0),
(34, 14, 79, '', 0),
(35, 14, 44, '', 0),
(36, 14, 9, '', 0),
(37, 14, 4, '', 0),
(38, 14, 90, '', 0),
(39, 14, 48, '', 0),
(40, 14, 58, '', 0),
(41, 23, 34, '', 0),
(42, 23, 66, '', 0),
(43, 23, 78, '', 0),
(44, 23, 39, '', 0),
(45, 23, 92, '', 0),
(46, 23, 57, '', 0),
(47, 23, 50, 'SELECT SUM(total_sales) AS total_sales_sum FROM Works_With;', 1),
(48, 23, 97, '', 0),
(49, 23, 77, '', 0),
(50, 23, 56, '', 0),
(51, 24, 90, '', 0),
(52, 24, 9, '', 0),
(53, 24, 88, '', 0),
(54, 24, 75, '', 0),
(55, 24, 93, '', 0),
(56, 24, 64, '', 0),
(57, 24, 36, 'SELECT SUM(total_sales) AS total_sales_sum FROM Works_With;', 0),
(58, 24, 22, '', 0),
(59, 24, 5, '', 0),
(60, 24, 39, '', 0),
(61, 25, 73, '', 0),
(62, 25, 81, '', 0),
(63, 25, 50, '', 0),
(64, 25, 1, '', 0),
(65, 25, 83, '', 0),
(66, 25, 41, '', 0),
(67, 25, 8, '', 0),
(68, 25, 69, '', 0),
(69, 25, 56, '', 0),
(70, 25, 63, '', 0),
(71, 26, 36, '', 0),
(72, 26, 90, '', 0),
(73, 26, 81, '', 0),
(74, 26, 1, '', 0),
(75, 26, 53, '', 0),
(76, 26, 66, '', 0),
(77, 26, 59, '', 0),
(78, 26, 73, '', 0),
(79, 26, 14, '', 0),
(80, 26, 5, 'SELECT * FROM Employee WHERE sex = \'F\';', 1),
(81, 27, 43, 'dwwdwdwdw', 0),
(82, 27, 14, '', 0),
(83, 27, 9, '', 0),
(84, 27, 91, '', 0),
(85, 27, 78, '', 0),
(86, 27, 73, '', 0),
(87, 27, 81, '', 0),
(88, 27, 70, '', 0),
(89, 27, 6, '', 0),
(90, 27, 58, '', 0),
(91, 28, 39, '', 0),
(92, 28, 9, '', 0),
(93, 28, 53, '', 0),
(94, 28, 78, '', 0),
(95, 28, 48, '', 0),
(96, 28, 71, '', 0),
(97, 28, 94, '', 0),
(98, 28, 47, '', 0),
(99, 28, 100, '', 0),
(100, 28, 65, '', 0),
(101, 29, 11, '', 0),
(102, 29, 50, '', 0),
(103, 29, 56, '', 0),
(104, 29, 53, '', 0),
(105, 29, 98, '', 0),
(106, 29, 48, '', 0),
(107, 29, 25, '', 0),
(108, 29, 37, '', 0),
(109, 29, 85, '', 0),
(110, 29, 72, '', 0),
(111, 30, 36, 'SELECT * FROM client WHERE LENGTH(client_name) > 10;', 1),
(112, 30, 89, '', 0),
(113, 30, 76, '', 0),
(114, 30, 78, '', 0),
(115, 30, 59, '', 0),
(116, 30, 50, 'SELECT SUM(total_sales) AS total_sales_sum FROM works_with', 1),
(117, 30, 2, '', 0),
(118, 30, 88, '', 0),
(119, 30, 27, 'SELECT * FROM branch_supplier WHERE supplier_name LIKE \'%Uni%\'', 1),
(120, 30, 26, 'SELECT * FROM employee WHERE last_name LIKE \'%n\'', 0),
(121, 31, 8, 'SELECT client_name FROM client', 1),
(122, 31, 1, '', 0),
(123, 31, 56, '', 0),
(124, 31, 47, '', 0),
(125, 31, 79, '', 0),
(126, 31, 26, '', 0),
(127, 31, 89, '', 0),
(128, 31, 69, '', 0),
(129, 31, 98, '', 0),
(130, 31, 58, '', 0),
(131, 32, 17, '', 0),
(132, 32, 79, '', 0),
(133, 32, 45, '', 0),
(134, 32, 50, '', 0),
(135, 32, 60, '', 0),
(136, 32, 70, '', 0),
(137, 32, 81, '', 0),
(138, 32, 46, '', 0),
(139, 32, 49, '', 0),
(140, 32, 96, '', 0),
(141, 34, 35, '', 0),
(142, 34, 24, '', 0),
(143, 34, 47, '', 0),
(144, 34, 32, '', 0),
(145, 34, 85, '', 0),
(146, 34, 76, '', 0),
(147, 34, 67, '', 0),
(148, 34, 25, '', 0),
(149, 34, 86, '', 0),
(150, 34, 70, '', 0),
(151, 35, 41, '', 0),
(152, 35, 59, '', 0),
(153, 35, 99, '', 0),
(154, 35, 90, '', 0),
(155, 35, 82, '', 0),
(156, 35, 73, '', 0),
(157, 35, 71, '', 0),
(158, 35, 28, '', 0),
(159, 35, 4, '', 0),
(160, 35, 24, '', 0),
(161, 36, 5, '', 0),
(162, 36, 65, '', 0),
(163, 36, 47, '', 0),
(164, 36, 17, '', 0),
(165, 36, 90, '', 0),
(166, 36, 62, '', 0),
(167, 36, 31, '', 0),
(168, 36, 53, '', 0),
(169, 36, 91, '', 0),
(170, 36, 89, '', 0),
(171, 37, 96, '', 0),
(172, 37, 93, '', 0),
(173, 37, 44, '', 0),
(174, 37, 57, '', 0),
(175, 37, 7, '', 0),
(176, 37, 6, '', 0),
(177, 37, 88, '', 0),
(178, 37, 71, '', 0),
(179, 37, 48, '', 0),
(180, 37, 55, '', 0),
(181, 38, 28, 'yy9y9y9yy', 0),
(182, 38, 27, '', 0),
(183, 38, 82, '', 0),
(184, 38, 77, '', 0),
(185, 38, 68, '', 0),
(186, 38, 39, '', 0),
(187, 38, 87, '', 0),
(188, 38, 50, '', 0),
(189, 38, 80, '', 0),
(190, 38, 37, '', 0),
(191, 39, 71, '', 0),
(192, 39, 94, 'SELECT e.emp_id, e.first_name, e.last_name FROM Employee e LEFT JOIN Works_With w ON e.emp_id = w.emp_id WHERE w.emp_id IS NULL;', 1),
(193, 39, 6, 'SELECT * FROM Employee WHERE branch_id = 2;', 1),
(194, 39, 1, '', 0),
(195, 39, 13, '', 0),
(196, 39, 75, '', 0),
(197, 39, 66, '', 0),
(198, 39, 56, '', 0),
(199, 39, 10, '', 0),
(200, 39, 48, '', 0),
(201, 40, 11, '', 0),
(202, 40, 91, '', 0),
(203, 40, 65, '', 0),
(204, 40, 13, '', 0),
(205, 40, 24, '', 0),
(206, 40, 57, '', 0),
(207, 40, 52, '', 0),
(208, 40, 77, '', 0),
(209, 40, 28, '', 0),
(210, 40, 59, '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `attempt_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `total_questions` int(11) DEFAULT NULL,
  `time_taken` varchar(10) DEFAULT NULL,
  `question_order` text DEFAULT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp(),
  `question_order_json` text DEFAULT NULL,
  `start_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`attempt_id`, `student_id`, `score`, `total_questions`, `time_taken`, `question_order`, `completed_at`, `last_activity`, `question_order_json`, `start_time`) VALUES
(1, 2, 0, 10, '02:07', NULL, '2025-09-11 22:19:17', '2025-09-11 22:19:17', NULL, '2025-09-17 13:12:54'),
(2, 1, 0, 10, '00:35', NULL, '2025-09-12 12:50:27', '2025-09-12 12:50:27', NULL, '2025-09-17 13:12:54'),
(3, 1, 20, 10, '131:19', NULL, '2025-09-12 15:01:11', '2025-09-12 15:01:11', NULL, '2025-09-17 13:12:54'),
(4, 1, 0, 10, '19', '[65,90,19,52,73,41,89,68,95,7]', '2025-09-12 15:33:33', '2025-09-12 15:33:33', NULL, '2025-09-17 13:12:54'),
(5, 1, 0, 10, '19', '[65,90,19,52,73,41,89,68,95,7]', '2025-09-12 15:33:33', '2025-09-12 15:33:33', NULL, '2025-09-17 13:12:54'),
(6, 1, 0, 10, '70', '[82,67,77,48,70,87,40,20,55,9]', '2025-09-12 15:34:55', '2025-09-12 15:34:55', NULL, '2025-09-17 13:12:54'),
(7, 1, 0, 10, '70', '[82,67,77,48,70,87,40,20,55,9]', '2025-09-12 15:34:55', '2025-09-12 15:34:55', NULL, '2025-09-17 13:12:54'),
(8, 1, 0, 10, '2469', '[81,48,92,90,36,21,43,88,11,13]', '2025-09-12 16:16:15', '2025-09-12 16:16:15', NULL, '2025-09-17 13:12:54'),
(9, 1, 0, 10, '2469', '[81,48,92,90,36,21,43,88,11,13]', '2025-09-12 16:16:15', '2025-09-12 16:16:15', NULL, '2025-09-17 13:12:54'),
(10, 1, 0, 10, '95', '[65,10,62,38,59,2,91,25,82,22]', '2025-09-12 16:18:01', '2025-09-12 16:18:01', NULL, '2025-09-17 13:12:54'),
(11, 1, 0, 10, '95', '[65,10,62,38,59,2,91,25,82,22]', '2025-09-12 16:18:01', '2025-09-12 16:18:01', NULL, '2025-09-17 13:12:54'),
(12, 1, 0, 10, '26', '[70,13,81,24,66,71,3,94,4,32]', '2025-09-12 16:18:41', '2025-09-12 16:18:41', NULL, '2025-09-17 13:12:54'),
(13, 1, 0, 10, '26', '[70,13,81,24,66,71,3,94,4,32]', '2025-09-12 16:18:41', '2025-09-12 16:18:41', NULL, '2025-09-17 13:12:54'),
(14, 1, 20, 10, '-175592369', '[14,98,88,79,44,9,4,90,48,58]', '2025-09-12 16:33:13', '2025-09-12 16:33:13', NULL, '2025-09-17 13:12:54'),
(15, 1, 0, 10, '867', '[14,98,88,79,44,9,4,90,48,58]', '2025-09-12 16:33:14', '2025-09-12 16:33:14', NULL, '2025-09-17 13:12:54'),
(16, 1, 0, 10, '-175593720', '[80,39,98,40,85,18,21,57,90,45]', '2025-09-12 16:35:13', '2025-09-12 16:35:13', NULL, '2025-09-17 13:12:54'),
(17, 1, 0, 10, '14', '[80,39,98,40,85,18,21,57,90,45]', '2025-09-12 16:35:13', '2025-09-12 16:35:13', NULL, '2025-09-17 13:12:54'),
(18, 1, 0, 10, '-175593720', '[62,21,93,44,73,90,16,100,76,14]', '2025-09-12 16:35:57', '2025-09-12 16:35:57', NULL, '2025-09-17 13:12:54'),
(19, 1, 0, 10, '30', '[62,21,93,44,73,90,16,100,76,14]', '2025-09-12 16:35:57', '2025-09-12 16:35:57', NULL, '2025-09-17 13:12:54'),
(20, 1, 0, 10, '-175593720', '[20,77,55,78,92,84,3,90,95,48]', '2025-09-12 16:49:46', '2025-09-12 16:49:46', NULL, '2025-09-17 13:12:54'),
(21, 1, 0, 10, '819', '[20,77,55,78,92,84,3,90,95,48]', '2025-09-12 16:49:46', '2025-09-12 16:49:46', NULL, '2025-09-17 13:12:54'),
(22, 1, 0, 10, '7', '[91,33,83,17,61,21,52,79,19,44]', '2025-09-12 17:05:06', '2025-09-12 17:05:06', NULL, '2025-09-17 13:12:54'),
(23, 1, 10, 10, '15:45', '[34,66,78,39,92,57,50,97,77,56]', '2025-09-12 18:26:57', '2025-09-12 18:26:57', NULL, '2025-09-17 13:12:54'),
(24, 1, 10, 10, '16:13', '[90,9,88,75,93,64,36,22,5,39]', '2025-09-12 18:27:24', '2025-09-12 18:27:24', NULL, '2025-09-17 13:12:54'),
(25, 1, 0, 10, '00:28', NULL, '2025-09-12 18:49:54', '2025-09-12 18:49:54', NULL, '2025-09-17 13:12:54'),
(26, 1, 10, 10, '01:50', NULL, '2025-09-14 01:36:08', '2025-09-14 01:36:08', NULL, '2025-09-17 13:12:54'),
(27, 1, 0, 10, '49:06', NULL, '2025-09-14 02:35:42', '2025-09-14 02:35:42', NULL, '2025-09-17 13:12:54'),
(28, 1, 0, 10, '00:07', NULL, '2025-09-14 02:36:23', '2025-09-14 02:36:23', NULL, '2025-09-17 13:12:54'),
(29, 1, 0, 10, '2643:01', NULL, '2025-09-15 22:39:18', '2025-09-15 22:39:18', NULL, '2025-09-17 13:12:54'),
(30, 1, 40, 10, '34:33', NULL, '2025-09-15 23:27:41', '2025-09-15 23:27:41', NULL, '2025-09-17 13:12:54'),
(31, 1, 10, 10, '06:44', NULL, '2025-09-15 23:53:07', '2025-09-15 23:53:07', NULL, '2025-09-17 13:12:54'),
(32, 1, 0, 10, '1335:34', NULL, '2025-09-17 12:18:55', '2025-09-17 12:18:55', NULL, '2025-09-17 13:12:54'),
(33, 1, 0, 10, '0', NULL, '2025-09-17 14:16:00', '2025-09-17 14:16:00', '[100,18,98,14,58,95,39,84,25,59]', '2025-09-17 14:16:00'),
(34, 1, 0, 10, '48:23', NULL, '2025-09-17 17:48:38', '2025-09-17 17:48:38', NULL, '2025-09-17 17:48:38'),
(35, 1, 0, 10, '79:58', NULL, '2025-09-17 18:20:12', '2025-09-17 18:20:12', NULL, '2025-09-17 18:20:12'),
(36, 1, 0, 10, '78:15', NULL, '2025-09-17 18:21:04', '2025-09-17 18:21:04', NULL, '2025-09-17 18:21:04'),
(37, 1, 0, 10, '00:12', NULL, '2025-09-17 19:21:19', '2025-09-17 19:21:19', NULL, '2025-09-17 19:21:19'),
(38, 1, 0, 10, '00:21', NULL, '2025-09-20 02:29:05', '2025-09-20 02:29:05', NULL, '2025-09-20 02:29:05'),
(39, 1, 30, 10, '04:25', NULL, '2025-09-20 02:40:05', '2025-09-20 02:40:05', NULL, '2025-09-20 02:40:05'),
(40, 1, 0, 10, '00:11', NULL, '2025-09-20 02:45:35', '2025-09-20 02:45:35', NULL, '2025-09-20 02:45:35');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'John Marfo', 'marfo444@gmail.com', '$2y$10$gOPdsUsh7IoIY94wWYF6hutAhFoHRYt6mkc6Ui1aekZlbbwTPYOQq', '2025-09-10 11:55:56'),
(2, 'Simon Peter', 'john444@gmail.com', '$2y$10$jV9pbcm8Lr80.PMbQGoW8O4ErVafqFEDH7Qp62rEZyZuojB5pDm6a', '2025-09-11 01:51:05'),
(3, 'KEKELI AGBATI', 'agbatikekeli2@gmail.com', '$2y$10$Yu/p38HSqP8GgKkncTLJ5Oapt4s8zc9xnxYiar21DUMBUV96EsqZy', '2025-09-11 14:18:12'),
(4, 'password johnny45', 'edge44@gmail.com', '$2y$10$jeB.YjlcZTn5wsftMUhkl.hNNmJjiS45kkwDvGvk6aGHQWl1NwihC', '2025-09-12 15:04:05');

-- --------------------------------------------------------

--
-- Table structure for table `works_with`
--

CREATE TABLE `works_with` (
  `emp_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `total_sales` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `works_with`
--

INSERT INTO `works_with` (`emp_id`, `client_id`, `total_sales`) VALUES
(102, 401, 267000),
(102, 406, 15000),
(105, 400, 55000),
(105, 404, 33000),
(105, 406, 130000),
(107, 403, 5000),
(107, 405, 26000),
(108, 402, 22500),
(108, 403, 12000);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `branch`
--
ALTER TABLE `branch`
  ADD PRIMARY KEY (`branch_id`),
  ADD KEY `mgr_id` (`mgr_id`);

--
-- Indexes for table `branch_supplier`
--
ALTER TABLE `branch_supplier`
  ADD PRIMARY KEY (`branch_id`,`supplier_name`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`client_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`emp_id`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `super_id` (`super_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`);

--
-- Indexes for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `attempt_id` (`attempt_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`attempt_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `works_with`
--
ALTER TABLE `works_with`
  ADD PRIMARY KEY (`emp_id`,`client_id`),
  ADD KEY `client_id` (`client_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=211;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `attempt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`);

--
-- Constraints for table `branch`
--
ALTER TABLE `branch`
  ADD CONSTRAINT `branch_ibfk_1` FOREIGN KEY (`mgr_id`) REFERENCES `employee` (`emp_id`) ON DELETE SET NULL;

--
-- Constraints for table `branch_supplier`
--
ALTER TABLE `branch_supplier`
  ADD CONSTRAINT `branch_supplier_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE CASCADE;

--
-- Constraints for table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `client_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE SET NULL;

--
-- Constraints for table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `employee_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_ibfk_2` FOREIGN KEY (`super_id`) REFERENCES `employee` (`emp_id`) ON DELETE SET NULL;

--
-- Constraints for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  ADD CONSTRAINT `quiz_answers_ibfk_1` FOREIGN KEY (`attempt_id`) REFERENCES `quiz_attempts` (`attempt_id`),
  ADD CONSTRAINT `quiz_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`);

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `works_with`
--
ALTER TABLE `works_with`
  ADD CONSTRAINT `works_with_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employee` (`emp_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `works_with_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `client` (`client_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
