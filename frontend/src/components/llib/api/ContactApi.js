export const getAllContacts = async () => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/contacts`, {
    method: "GET",
    headers: {
      Accept: "application/json",
    },
  });
};

export const adminUploadContact = async (token, { platform_name, url, icon }) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/contacts`, {
    method: "POST",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify({
      platform_name,
      url,
      icon,
    }),
  });
};

export const adminUpdateContact = async (token, { platform_name, url, icon }) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/contacts/${id}`, {
    method: "PUT",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify({
      platform_name,
      url,
      icon,
    }),
  });
};

export const adminDeleteContact = async (token) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/contacts/${id}`, {
    method: "DELETE",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
};
