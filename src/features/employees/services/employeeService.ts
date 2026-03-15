import { apiClient } from "../../../services/apiClient";
import type { ApiDetailResponse, ApiListResponse } from "../../../types/api.types";
import type {
  EmployeeListItem,
  EmployeeListParams,
  EmployeeRecord,
} from "../types/employee.types";

function createQueryString(params: EmployeeListParams) {
  const query = new URLSearchParams();

  query.set("per_page", String(params.per_page ?? 100));

  if (params.query && params.query.trim().length > 0) {
    query.set("query", params.query.trim());
  }

  if (params.status && params.status !== "all") {
    query.set("status", params.status);
  }

  if (params.department_id) {
    query.set("department_id", params.department_id);
  }

  if (params.position_id) {
    query.set("position_id", params.position_id);
  }

  return query.toString();
}

export const employeeService = {
  async getEmployees(params: EmployeeListParams = {}): Promise<EmployeeListItem[]> {
    const queryString = createQueryString(params);
    const employeesResponse = await apiClient.get<ApiListResponse<EmployeeRecord>>(`/employees?${queryString}`);

    return employeesResponse.data.data.map((employee) => ({
      id: employee.id,
      code: employee.employee_code,
      name: employee.user_name ?? "-",
      department: employee.department_name ?? "-",
      position: employee.position_name ?? "-",
      status: employee.status ?? "-",
    }));
  },

  async getEmployee(id: string): Promise<EmployeeRecord> {
    const response = await apiClient.get<ApiDetailResponse<EmployeeRecord>>(`/employees/${id}`);
    return response.data.data;
  },

  async createEmployee(payload: {
    user_id: string;
    employee_code: string;
    department_id: string;
    position_id: string;
    phone?: string;
    hire_date?: string;
    status?: string;
  }): Promise<void> {
    await apiClient.post("/employees", payload);
  },

  async updateEmployee(
    id: string,
    payload: {
      user_id: string;
      employee_code: string;
      department_id: string;
      position_id: string;
      phone?: string;
      hire_date?: string;
      status?: string;
    },
  ): Promise<void> {
    await apiClient.put(`/employees/${id}`, payload);
  },

  async deleteEmployee(id: string): Promise<void> {
    await apiClient.delete(`/employees/${id}`);
  },
};
