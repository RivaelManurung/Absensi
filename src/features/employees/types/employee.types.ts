export type EmployeeRecord = {
  id: string;
  user_id: string;
  employee_code: string;
  department_id: string;
  position_id: string;
  phone?: string | null;
  hire_date?: string | null;
  status?: string | null;
  created_at?: string;
  updated_at?: string;
  user_name?: string | null;
  user_email?: string | null;
  department_name?: string | null;
  position_name?: string | null;
};

export type DepartmentRecord = {
  id: string;
  name: string;
};

export type PositionRecord = {
  id: string;
  name: string;
};

export type EmployeeListItem = {
  id: string;
  code: string;
  name: string;
  department: string;
  position: string;
  status: string;
};

export type EmployeesFilters = {
  query: string;
};

export type EmployeeListParams = {
  per_page?: number;
  query?: string;
  status?: string;
  department_id?: string;
  position_id?: string;
};
