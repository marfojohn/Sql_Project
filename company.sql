-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 11, 2025 at 04:13 AM
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
(10, 'Get details of employee \"Michael Scott\".', 'SELECT * FROM Employee WHERE first_name = \'Michael\' AND last_name = \'Scott\';', NULL, 'basic'),
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
(50, 'Calculate the sum of all sales.', 'SELECT SUM(total_sales) AS total_sales_sum FROM Works_With;', NULL, 'basic'),
(51, 'Find the average total_sales per employee.', 'SELECT emp_id, AVG(total_sales) AS avg_sales FROM Works_With GROUP BY emp_id;', NULL, 'intermediate'),
(52, 'Count how many suppliers each branch has.', 'SELECT branch_id, COUNT(*) AS supplier_count FROM Branch_Supplier GROUP BY branch_id;', NULL, 'intermediate'),
(53, 'Find the supplier count for branch_id = 2.', 'SELECT COUNT(*) AS supplier_count FROM Branch_Supplier WHERE branch_id = 2;', NULL, 'basic'),
(54, 'Get the number of employees supervised by emp_id = 100.', 'SELECT COUNT(*) AS subordinates FROM Employee WHERE super_id = 100;', NULL, 'basic'),
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
(1, 7, 21, 'SELECT * FROM employee WHERE \nsalary BETWEEN 60000 AND 100000', 0),
(2, 8, 57, 'SELECT * FROM employee WHERE \nsalary BETWEEN 60000 AND 100000', 0),
(3, 9, 74, 'SELECT * FROM employee WHERE \nsalary BETWEEN 60000 AND 100000', 0),
(4, 9, 37, '', 0),
(5, 9, 50, '', 0),
(6, 9, 82, '', 0),
(7, 9, 51, '', 0),
(8, 9, 97, '', 0),
(9, 9, 19, '', 0),
(10, 9, 12, '', 0),
(11, 9, 78, '', 0),
(12, 9, 73, '', 0),
(13, 10, 51, '', 0),
(14, 10, 82, '', 0),
(15, 10, 88, '', 0),
(16, 10, 94, '', 0),
(17, 10, 58, '', 0),
(18, 10, 71, '', 0),
(19, 10, 28, '', 0),
(20, 10, 5, '', 0),
(21, 10, 11, '', 0),
(22, 10, 23, '', 0),
(23, 11, 45, '', 0),
(24, 11, 43, '', 0),
(25, 11, 93, '', 0),
(26, 11, 27, '', 0),
(27, 11, 78, '', 0),
(28, 11, 66, '', 0),
(29, 11, 39, '', 0),
(30, 11, 100, '', 0),
(31, 11, 75, '', 0),
(32, 11, 90, '', 0),
(33, 12, 69, '', 0),
(34, 12, 19, '', 0),
(35, 12, 81, '', 0),
(36, 12, 43, '', 0),
(37, 12, 51, '', 0),
(38, 12, 96, '', 0),
(39, 12, 8, '', 0),
(40, 12, 47, '', 0),
(41, 12, 84, '', 0),
(42, 12, 63, '', 0),
(43, 13, 100, '', 0),
(44, 13, 56, '', 0),
(45, 13, 48, '', 0),
(46, 13, 25, '', 0),
(47, 13, 47, '', 0),
(48, 13, 28, '', 0),
(49, 13, 76, '', 0),
(50, 13, 24, '', 0),
(51, 13, 73, '', 0),
(52, 13, 90, '', 0),
(53, 14, 23, '', 0),
(54, 14, 48, '', 0),
(55, 14, 93, '', 0),
(56, 14, 1, '', 0),
(57, 14, 53, '', 0),
(58, 14, 56, '', 0),
(59, 14, 96, '', 0),
(60, 14, 41, '', 0),
(61, 14, 81, '', 0),
(62, 14, 84, '', 0),
(63, 15, 4, '', 0),
(64, 15, 59, '', 0),
(65, 15, 5, '', 0),
(66, 15, 39, '', 0),
(67, 15, 100, '', 0),
(68, 15, 17, '', 0),
(69, 15, 48, '', 0),
(70, 15, 64, '', 0),
(71, 15, 95, '', 0),
(72, 15, 82, '', 0),
(73, 16, 84, '', 0),
(74, 16, 53, 'SELECT e.first_name, e.last_name, SUM(w.total_sales) AS total_sales FROM Employee e JOIN Works_With w ON e.emp_id = w.emp_id GROUP BY e.emp_id, e.first_name, e.last_name;', 0),
(75, 16, 97, '', 0),
(76, 16, 70, '', 0),
(77, 16, 20, '', 0),
(78, 16, 58, '', 0),
(79, 16, 75, 'SELECT DISTINCT first_name, last_name\nFROM Employee\nNATURAL JOIN Works_With\nNATURAL JOIN Client\nWHERE client_name = \'FedEx\';', 1),
(80, 16, 56, 'SELECT w.client_id, t.client_sales\nFROM Works_With w\nJOIN (\n    SELECT client_id, SUM(total_sales) AS client_sales\n    FROM Works_With\n    GROUP BY client_id\n) t ON w.client_id = t.client_id\nGROUP BY w.client_id, t.client_sales;', 1),
(81, 16, 25, '', 0),
(82, 16, 76, '', 0),
(83, 17, 53, '', 0),
(84, 17, 67, '', 0),
(85, 17, 54, '', 0),
(86, 17, 52, '', 0),
(87, 17, 14, '', 0),
(88, 17, 78, '', 0),
(89, 17, 96, '', 0),
(90, 17, 66, 'SELECT e.first_name, e.last_name, t.total_sales\nFROM Employee e\nJOIN (\n    SELECT emp_id, SUM(total_sales) AS total_sales\n    FROM Works_With\n    GROUP BY emp_id\n) t ON e.emp_id = t.emp_id;', 1),
(91, 17, 40, '', 0),
(92, 17, 90, '', 0),
(93, 18, 1, '', 0),
(94, 18, 98, '', 0),
(95, 18, 53, '', 0),
(96, 18, 70, '', 0),
(97, 18, 49, '', 0),
(98, 18, 92, '', 0),
(99, 18, 66, '', 0),
(100, 18, 33, 'SELECT e.first_name, e.last_name, t.total_sales\nFROM Employee e\nJOIN (\n    SELECT emp_id, SUM(total_sales) AS total_sales\n    FROM Works_With\n    GROUP BY emp_id\n) t ON e.emp_id = t.emp_id;', 0),
(101, 18, 38, '', 0),
(102, 18, 63, '', 0);

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
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`attempt_id`, `student_id`, `score`, `total_questions`, `time_taken`, `completed_at`) VALUES
(1, 1, 0, 10, '56:13', '2025-09-10 20:52:19'),
(2, 1, 0, 10, '57:32', '2025-09-10 20:53:38'),
(3, 1, 0, 10, '68:44', '2025-09-10 21:04:50'),
(4, 1, 0, 10, '68:50', '2025-09-10 21:04:56'),
(5, 1, 0, 10, '69:00', '2025-09-10 21:05:06'),
(6, 1, 0, 10, '70:20', '2025-09-10 21:06:26'),
(7, 1, 10, 10, '78:44', '2025-09-10 21:14:51'),
(8, 1, 10, 10, '87:24', '2025-09-10 21:23:30'),
(9, 1, 10, 10, '110:21', '2025-09-10 21:46:27'),
(10, 1, 0, 10, '02:02', '2025-09-10 22:02:55'),
(11, 1, 0, 10, '02:40', '2025-09-10 22:03:34'),
(12, 1, 0, 10, '03:13', '2025-09-10 22:04:06'),
(13, 1, 0, 10, '14:39', '2025-09-10 22:15:32'),
(14, 1, 0, 10, '35:30', '2025-09-10 22:41:12'),
(15, 1, 0, 10, '26:15', '2025-09-11 00:00:32'),
(16, 1, 190, 10, '14:24', '2025-09-11 01:09:50'),
(17, 1, 60, 10, '05:20', '2025-09-11 01:16:48'),
(18, 1, 30, 10, '05:56', '2025-09-11 01:17:24');

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
(2, 'Simon Peter', 'john444@gmail.com', '$2y$10$jV9pbcm8Lr80.PMbQGoW8O4ErVafqFEDH7Qp62rEZyZuojB5pDm6a', '2025-09-11 01:51:05');

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
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `attempt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
