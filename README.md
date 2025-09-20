# SQL Master Web App - Project Documentation

## Project Overview

**SQL Master** is a comprehensive web application designed to help students practice and master SQL query writing skills. Developed during my internship at NIT Open Labs Ghana, this interactive platform provides real-time query validation, scoring, and performance tracking.

### Key Features

1. **User Authentication System** - Secure login and registration
2. **Interactive Quiz Interface** - Dynamic question presentation with SQL editor
3. **Real-time Query Validation** - Immediate feedback on query correctness
4. **Scoring System** - Points awarded based on question difficulty
5. **Progress Tracking** - Session persistence and performance review
6. **Responsive Design** - Works on desktop and mobile devices
7. **Dark/Light Theme** - User preference customization

## Technical Architecture

### Frontend Technologies
- HTML5, CSS3 with custom animations and responsive design
- JavaScript (ES6+) for interactive functionality
- CodeMirror for SQL syntax highlighting and editing
- Font Awesome for icons

### Backend Technologies
- PHP for server-side processing and API endpoints
- MySQL/MariaDB for data storage
- Session management for user state

### Database Schema
The application uses a company database with the following structure:
- **Employee** (emp_id, first_name, last_name, birth_date, sex, salary, super_id, branch_id)
- **Branch** (branch_id, branch_name, mgr_id, mgr_start_date)
- **Client** (client_id, client_name, branch_id)
- **Branch_Supplier** (branch_id, supplier_name, supply_type)

## File Structure and Functionality

### Core Application Files

1. **index.php** - Landing page with quiz information and rules
2. **login.php** - User authentication system
3. **signup.php** - New user registration
4. **quiz.php** - Main quiz interface with SQL editor
5. **review.php** - Quiz results and answer review
6. **logout.php** - Session termination

### API Endpoints

1. **execute_query.php** - Processes SQL queries (security-limited to SELECT)
2. **validate_query.php** - Compares user queries with correct solutions
3. **get_question.php** - Retrieves quiz questions from database
4. **save_quiz_state.php** - Persists quiz progress
5. **clear_quiz_session.php** - Resets quiz data

### Configuration Files

1. **config/database.php** - Database connection settings
2. **quiz.css** - Styling for quiz interface
3. **review.css** - Styling for review page

## Key Functionality Details

### Quiz Mechanics

1. **Question Selection**: 10 questions (4 basic, 3 intermediate, 3 advanced)
2. **Scoring System**:
   - Basic questions: 10 points each
   - Intermediate questions: 20 points each
   - Advanced questions: 30 points each
   - Maximum score: 190 points

3. **Time Limit**: 30 minutes per quiz session
4. **Query Validation**: Compares result sets rather than exact query syntax

### Security Features

1. SQL injection prevention through parameterized queries
2. Session-based authentication
3. Query execution limited to SELECT statements only
4. Input validation and sanitization

## Installation and Setup

### Prerequisites
- Web server (Apache/Nginx)
- PHP 7.4 or higher
- MySQL/MariaDB 5.7 or higher
- Modern web browser with JavaScript enabled

### Installation Steps

1. Clone or extract project files to web server directory
2. Create MySQL database and import provided schema
3. Update database credentials in `config/database.php`
4. Ensure proper file permissions for session storage
5. Access application through web browser

### Database Setup

The application requires the following tables:
- `students` - User accounts
- `questions` - Quiz questions and solutions
- `quiz_attempts` - Quiz performance history
- `quiz_answers` - Individual question responses

## Usage Workflow

1. **Registration/Login**: Users create accounts or log in
2. **Quiz Start**: Users begin a new quiz with randomized questions
3. **Query Writing**: Users write SQL queries in the CodeMirror editor
4. **Validation**: System executes and compares queries with expected results
5. **Progress Tracking**: Scores update in real-time with visual indicators
6. **Review**: After completion, users can review their answers and correct solutions

## Customization Options

### Question Management
Add new questions to the database with appropriate difficulty levels:

```sql
INSERT INTO questions (question_text, correct_query, difficulty) 
VALUES ('Your question text', 'SELECT correct_solution', 'basic');
```

### Styling Modifications
- Modify CSS variables in `:root` declarations for color scheme changes
- Update animations and transitions in CSS files
- Adjust responsive breakpoints for different device support

### Security Enhancements
- Implement additional validation in API endpoints
- Add rate limiting for query execution
- Enhance input sanitization procedures

## Performance Considerations

1. **Database Indexing**: Ensure proper indexing on frequently queried columns
2. **Query Optimization**: Review and optimize correct query solutions
3. **Caching**: Implement caching for static resources and database results
4. **Session Management**: Monitor session storage and cleanup procedures

## Browser Compatibility

- Chrome 70+
- Firefox 65+
- Safari 12+
- Edge 79+

## Future Enhancements

1. Additional database schemas for varied practice
2. Social features (leaderboards, challenges)
3. Advanced query analysis and suggestions
4. Mobile application version
5. Integration with learning management systems

## Developer Information

**Name**: John Kusi Marfo  
**Student ID**: 052241360094  
**Institution**: Kumasi Technical University  
**Internship**: NIT Open Labs Ghana

## License

This project is developed for educational purposes. Please ensure proper attribution when using or modifying the code.

---

This documentation provides a comprehensive overview of the SQL Master web application. For additional details or support, please refer to the code comments or contact the developer.