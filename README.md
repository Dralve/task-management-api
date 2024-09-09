# Task Management API

## Overview

The Task Management API is a Laravel-based application designed to handle task management with a focus on role-based permissions. It allows users to create, update, view, delete, and assign tasks. The API supports different user roles, including Admin, Manager, and User, with specific permissions for each role. The project also includes advanced features such as soft deletes, date formatting, and query scopes for filtering tasks.

## Features

- **Task Management**: CRUD operations for tasks.
- **Task Assignment**: Assign tasks to users with specific role-based permissions.
- **Role Management**: Different permissions for Admin, Manager, and User roles.
- **Date Handling**: Accessors and Mutators for date formatting.
- **Soft Deletes**: Restore tasks and users after deletion.
- **Advanced Query Scopes**: Filtering tasks by priority and status.

## Project Setup

### Requirements

- PHP >= 8.0
- Composer
- Laravel >= 9.x
- MySQL or another database

### Installation

1. **Clone the Repository**

    ```bash
    git clone https://github.com/Dralve/task-management-api.git
    ```

2. **Navigate to the Project Directory**

    ```bash
    cd task-management-api
    ```

3. **Install Dependencies**

    ```bash
    composer install
    ```

4. **Set Up Environment Variables**

    Copy the `.env.example` file to `.env` and configure your database and other environment settings.

    ```bash
    cp .env.example .env
    ```

    Update the `.env` file with your database credentials and other configuration details.


5. **Run Migrations**

    ```bash
    php artisan migrate
    ```

6. **Seed the Database (To Make Admin And Manager)**

    ```bash
    php artisan db:seed
    ```

7. **Start the Development Server**

    ```bash
    php artisan serve
    ```

## API Endpoints

### Authentication

- **Login**: `POST /api/auth/v1/login`
- **Register**: `POST /api/auth/v1/register`
- **Logout**: `POST api/auth/v1/logout`
- **Refresh**: `POST api/auth/v1/refresh`
- **current**: `GET api/auth/v1/current/user`

### Tasks

- **Create Book**: `POST /api/v1/tasks`
- **View Books**: `GET /api/v1/tasks`
- **Update Book**: `PUT /api/v1/tasks/{id}`
- **Delete Book**: `DELETE /api/v1/tasks/{id}`
- **Assign Task**: `POST /api/v1/tasks/{id}/assign`
- **Restore Task**: `POST /api/v1/tasks?priority&status`
- **Filtering**: `GET /api/v1/tasks/{id}/restore`

### Users

- **Create User**: `POST /api/v1/users`
- **View Users**: `GET /api/v1/users`
- **Update User**: `PUT /api/v1/users/{id}`
- **Delete User**: `DELETE /api/v1/users/{id}`
- **Restore User**: `POST /api/v1/users/{id}/restore`


## Validation Rules

- **TaskFormRequest**: Validates Tasks data including title, description, priority, due_date, status, assigned_to, and created_by.

## Error Handling

Customized error messages and responses are provided to ensure clarity and user-friendly feedback.

## Documentation

All code is documented with appropriate comments and DocBlocks. For more details on the codebase, refer to the inline comments.

## Contributing

Contributions are welcome! Please follow the standard pull request process and adhere to the project's coding standards.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contact

For any questions or issues, please contact [your email] or open an issue on GitHub.

