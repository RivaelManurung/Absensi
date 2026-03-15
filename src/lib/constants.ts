export const APP_ROUTES = {
  home: "/",
  login: "/login",
  users: "/users",
  usersCreate: "/users/create",
  usersEdit: "/users/:id/edit",
  usersDetail: "/users/:id",
  employees: "/employees",
  employeesCreate: "/employees/create",
  employeesDetail: "/employees/:id",
  barcodes: "/barcodes",
  barcodesGenerate: "/barcodes/generate",
  barcodesDetail: "/barcodes/:id",
  attendance: "/attendance",
  attendanceDetail: "/attendance/:id",
  activityLogs: "/activity-logs",
  activityLogsDetail: "/activity-logs/:id",
  roles: "/roles",
  permissions: "/permissions",
} as const;

export const ROUTE_ACCESS = {
  home: {
    allowedRoles: ["Admin", "HR", "Employee", "Supervisor", "Manager"],
  },
  users: {
    allowedRoles: ["Admin", "HR"],
    requiredPermissions: ["users.view"],
  },
  employees: {
    allowedRoles: ["Admin", "HR", "Manager"],
    requiredPermissions: ["employees.view"],
  },
  barcodes: {
    allowedRoles: ["Admin", "HR", "Supervisor", "Manager"],
    requiredPermissions: ["barcodes.view"],
  },
  attendance: {
    allowedRoles: ["Admin", "HR", "Employee", "Supervisor", "Manager"],
    requiredPermissions: ["attendance.view"],
  },
  activityLogs: {
    allowedRoles: ["Admin", "HR", "Manager"],
    requiredPermissions: ["activity-logs.view"],
  },
  roles: {
    allowedRoles: ["Admin"],
    requiredPermissions: ["roles.view"],
  },
  permissions: {
    allowedRoles: ["Admin"],
    requiredPermissions: ["permissions.view"],
  },
} as const;
