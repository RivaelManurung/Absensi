export type UserRecord = {
  id: string;
  name: string;
  email: string;
  is_active: boolean;
  created_at?: string;
  updated_at?: string;
  role_name?: string | null;
};

export type UserRoleRecord = {
  id: string;
  user_id: string;
  role_id: string;
};

export type RoleRecord = {
  id: string;
  name: string;
};

export type UserListItem = {
  id: string;
  name: string;
  email: string;
  role: string;
  status: "Active" | "Inactive";
  createdDate: string;
};

export type UsersFilters = {
  query: string;
  role: string;
  status: "all" | "active" | "inactive";
};

export type UserListParams = {
  per_page?: number;
  query?: string;
  role?: string;
  status?: UsersFilters["status"];
};
