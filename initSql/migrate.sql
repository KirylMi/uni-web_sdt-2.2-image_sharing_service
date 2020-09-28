CREATE TABLE public.users
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 ),
    email text NOT NULL,
    username text NOT NULL,
    password text NOT NULL,
    privilege text NOT NULL,
    name text NOT NULL,
    active boolean NOT NULL DEFAULT false,
    PRIMARY KEY (id),
    UNIQUE (id), UNIQUE (email), UNIQUE (name), UNIQUE (username)
);

ALTER TABLE public.users
    OWNER to postgres;


CREATE TABLE public."usersActivation"
(
    username text NOT NULL,
    code text NOT NULL,
    CONSTRAINT "usersActivation_pkey" PRIMARY KEY (username)
    --CONSTRAINT "usersActivation_username_key" UNIQUE (username)
    --username text COLLATE pg_catalog."default" NOT NULL,
    --code text COLLATE pg_catalog."default" NOT NULL,
    --CONSTRAINT activation_pkey PRIMARY KEY (username),
    --CONSTRAINT "usersActivation_username_key" UNIQUE (username)
);

ALTER TABLE public."usersActivation"
    OWNER to postgres;
COMMENT ON TABLE public."usersActivation"
    IS 'stores the activation codes';


CREATE TABLE public."usersImages"
(
    username text NOT NULL,
    images text[],
    CONSTRAINT "usersImages_pkey" PRIMARY KEY (username)
    --CONSTRAINT "usersImages_username_key" UNIQUE (username)
);

ALTER TABLE public."usersImages"
    OWNER to postgres;
COMMENT ON TABLE public."usersImages"
    IS 'stores the names of the image files';


CREATE TABLE public.subscriptions
(
    username text  NOT NULL,
    subscriptions text[],
    CONSTRAINT subscriptions_pkey PRIMARY KEY (username)
);

ALTER TABLE public.subscriptions
    OWNER to postgres;
COMMENT ON TABLE public.subscriptions
    IS 'This table shows what subscriptions user has';


CREATE TABLE public."usersComments"
(
    "targetUser" text NOT NULL,
    commentator text[],
    "imageName" text[],
    comment text[],
    CONSTRAINT "userComments_pkey" PRIMARY KEY ("targetUser")
);

ALTER TABLE public."usersComments"
OWNER to postgres;
COMMENT ON TABLE public."usersComments"
IS 'This table stores all comments TO the specified user. So the PK is targetUser';

