import type { ReactNode } from "react";
import { Navigate, Route, Routes } from "react-router-dom";
import { DashboardLayout } from "../../components/layout/DashboardLayout";
import { ActivityLogsDetailPage } from "../../features/activity-logs/pages/ActivityLogsDetailPage";
import { ActivityLogsIndexPage } from "../../features/activity-logs/pages/ActivityLogsIndexPage";
import { APP_ROUTES, ROUTE_ACCESS } from "../../lib/constants";
import { LoginPage } from "../../features/auth/pages/LoginPage";
import { BarcodesDetailPage } from "../../features/barcodes/pages/BarcodesDetailPage";
import { BarcodesGeneratePage } from "../../features/barcodes/pages/BarcodesGeneratePage";
import { BarcodesIndexPage } from "../../features/barcodes/pages/BarcodesIndexPage";
import { AttendanceDetailPage } from "../../features/attendance/pages/AttendanceDetailPage";
import { AttendanceIndexPage } from "../../features/attendance/pages/AttendanceIndexPage";
import { DashboardPage } from "../../features/dashboard/pages/DashboardPage";
import { EmployeesCreatePage } from "../../features/employees/pages/EmployeesCreatePage";
import { EmployeesDetailPage } from "../../features/employees/pages/EmployeesDetailPage";
import { EmployeesIndexPage } from "../../features/employees/pages/EmployeesIndexPage";
import { PermissionsIndexPage } from "../../features/permissions/pages/PermissionsIndexPage";
import { RolesIndexPage } from "../../features/roles/pages/RolesIndexPage";
import { UsersCreatePage } from "../../features/users/pages/UsersCreatePage";
import { UsersDetailPage } from "../../features/users/pages/UsersDetailPage";
import { UsersEditPage } from "../../features/users/pages/UsersEditPage";
import { UsersIndexPage } from "../../features/users/pages/UsersIndexPage";
import { RequireAccess, RequireAuth, RequireGuest } from "./RouteGuards";

type ProtectedPageProps = {
  children: ReactNode;
  allowedRoles?: readonly string[];
  requiredPermissions?: readonly string[];
};

function ProtectedPage({ children, allowedRoles, requiredPermissions }: ProtectedPageProps) {
  return (
    <RequireAuth>
      <RequireAccess allowedRoles={allowedRoles} requiredPermissions={requiredPermissions}>
        <DashboardLayout>{children}</DashboardLayout>
      </RequireAccess>
    </RequireAuth>
  );
}

export function AppRouter() {
  return (
    <Routes>
      <Route
        path={APP_ROUTES.login}
        element={
          <RequireGuest>
            <LoginPage />
          </RequireGuest>
        }
      />
      <Route
        path={APP_ROUTES.home}
        element={
          <ProtectedPage {...ROUTE_ACCESS.home}>
            <DashboardPage />
          </ProtectedPage>
        }
      />
      <Route
        path={APP_ROUTES.users}
        element={
          <ProtectedPage {...ROUTE_ACCESS.users}>
            <UsersIndexPage />
          </ProtectedPage>
        }
      />
      <Route
        path={APP_ROUTES.usersCreate}
        element={
          <ProtectedPage {...ROUTE_ACCESS.users}>
            <UsersCreatePage />
          </ProtectedPage>
        }
      />
      <Route
        path={APP_ROUTES.usersEdit}
        element={
          <ProtectedPage {...ROUTE_ACCESS.users}>
            <UsersEditPage />
          </ProtectedPage>
        }
      />
      <Route
        path={APP_ROUTES.usersDetail}
        element={
          <ProtectedPage {...ROUTE_ACCESS.users}>
            <UsersDetailPage />
          </ProtectedPage>
        }
      />
      <Route
        path={APP_ROUTES.employees}
        element={
          <ProtectedPage {...ROUTE_ACCESS.employees}>
            <EmployeesIndexPage />
          </ProtectedPage>
        }
      />
      <Route
        path={APP_ROUTES.employeesCreate}
        element={
          <ProtectedPage {...ROUTE_ACCESS.employees}>
            <EmployeesCreatePage />
          </ProtectedPage>
        }
      />
      <Route
        path={APP_ROUTES.employeesDetail}
        element={
          <ProtectedPage {...ROUTE_ACCESS.employees}>
            <EmployeesDetailPage />
          </ProtectedPage>
        }
      />
      <Route
        path={APP_ROUTES.barcodes}
        element={
          <ProtectedPage {...ROUTE_ACCESS.barcodes}>
            <BarcodesIndexPage />
          </ProtectedPage>
        }
      />
      <Route
        path={APP_ROUTES.barcodesGenerate}
        element={
          <ProtectedPage {...ROUTE_ACCESS.barcodes}>
            <BarcodesGeneratePage />
          </ProtectedPage>
        }
      />
      <Route
        path={APP_ROUTES.barcodesDetail}
        element={
          <ProtectedPage {...ROUTE_ACCESS.barcodes}>
            <BarcodesDetailPage />
          </ProtectedPage>
        }
      />
      <Route
        path={APP_ROUTES.attendance}
        element={
          <ProtectedPage {...ROUTE_ACCESS.attendance}>
            <AttendanceIndexPage />
          </ProtectedPage>
        }
      />
      <Route
        path={APP_ROUTES.attendanceDetail}
        element={
          <ProtectedPage {...ROUTE_ACCESS.attendance}>
            <AttendanceDetailPage />
          </ProtectedPage>
        }
      />
      <Route
        path={APP_ROUTES.activityLogs}
        element={
          <ProtectedPage {...ROUTE_ACCESS.activityLogs}>
            <ActivityLogsIndexPage />
          </ProtectedPage>
        }
      />
      <Route
        path={APP_ROUTES.activityLogsDetail}
        element={
          <ProtectedPage {...ROUTE_ACCESS.activityLogs}>
            <ActivityLogsDetailPage />
          </ProtectedPage>
        }
      />
      <Route
        path={APP_ROUTES.roles}
        element={
          <ProtectedPage {...ROUTE_ACCESS.roles}>
            <RolesIndexPage />
          </ProtectedPage>
        }
      />
      <Route
        path={APP_ROUTES.permissions}
        element={
          <ProtectedPage {...ROUTE_ACCESS.permissions}>
            <PermissionsIndexPage />
          </ProtectedPage>
        }
      />
      <Route path="*" element={<Navigate to={APP_ROUTES.home} replace />} />
    </Routes>
  );
}
