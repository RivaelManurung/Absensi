import { type PropsWithChildren } from "react";
import { Navigate } from "react-router-dom";
import { useAuth } from "../providers/AuthProvider";
import { APP_ROUTES } from "../../lib/constants";

type AccessGuardProps = PropsWithChildren<{
  allowedRoles?: readonly string[];
  requiredPermissions?: readonly string[];
}>;

export function RequireAuth({ children }: PropsWithChildren) {
  const { user, isLoading } = useAuth();

  if (isLoading) {
    return <p className="p-6 text-sm text-muted-foreground">Loading session...</p>;
  }

  if (!user) {
    return <Navigate to={APP_ROUTES.login} replace />;
  }

  return <>{children}</>;
}

export function RequireGuest({ children }: PropsWithChildren) {
  const { user, isLoading } = useAuth();

  if (isLoading) {
    return <p className="p-6 text-sm text-muted-foreground">Loading session...</p>;
  }

  if (user) {
    return <Navigate to={APP_ROUTES.home} replace />;
  }

  return <>{children}</>;
}

export function RequireAccess({ children, allowedRoles = [], requiredPermissions = [] }: AccessGuardProps) {
  const { user } = useAuth();

  if (!user) {
    return <Navigate to={APP_ROUTES.login} replace />;
  }

  const hasRole = allowedRoles.length === 0 || user.roles.some((role) => allowedRoles.includes(role));
  const hasPermission =
    requiredPermissions.length === 0 || requiredPermissions.some((permission) => user.permissions.includes(permission));

  if (!hasRole && !hasPermission) {
    return <Navigate to={APP_ROUTES.home} replace />;
  }

  return <>{children}</>;
}
