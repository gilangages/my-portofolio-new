export const logVisitor = async (deviceId) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/visitors`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
    },
    body: JSON.stringify({ device_id: deviceId }),
  });
};

export const getVisitorCount = async () => {
  const token = localStorage.getItem("token");
  return await fetch(`${import.meta.env.VITE_APP_PATH}/admin/visitors/count`, {
    method: "GET",
    headers: {
      Authorization: `Bearer ${token}`,
      Accept: "application/json",
    },
  });
};
