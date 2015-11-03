-- 20151010 Version 11
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- Name: plpgsql; Type: PROCEDURAL LANGUAGE; Schema: -; Owner: postgres
--

CREATE PROCEDURAL LANGUAGE plpgsql;


ALTER PROCEDURAL LANGUAGE plpgsql OWNER TO postgres;

SET search_path = public, pg_catalog;

create user nnuser with password 'temppass';
alter user nnuser with login;
create user ronnuser with password 'temppass';
alter user ronnuser with login;
create tablespace nnts owner nnuser location '/data/pgsql/nndata2';
create database nnbase2 owner nnuser tablespace nnts;

\c nnbase2

--
-- Name: perfdata_insert(); Type: FUNCTION; Schema: public; Owner: nnuser
--

CREATE FUNCTION perfdata_insert() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
  if ((date(NEW.daytime)-date'1970-01-01') % 4 = 0 ) then
    insert into perfdata_0 values (new.*);
  elsif ((date(NEW.daytime)-date'1970-01-01') % 4 = 1 ) then
    insert into perfdata_1 values (new.*);
  elsif ((date(NEW.daytime)-date'1970-01-01') % 4 = 2 ) then
    insert into perfdata_2 values (new.*);
  elsif ((date(NEW.daytime)-date'1970-01-01') % 4 = 3 ) then
    insert into perfdata_3 values (new.*);
  end if;
  return null;
end;
$$;


ALTER FUNCTION public.perfdata_insert() OWNER TO nnuser;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: hosts; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE hosts (
    host character varying NOT NULL,
    os character varying,
    lparname character varying,
    serial character varying,
    vio numeric(1,0)
);


ALTER TABLE public.hosts OWNER TO nnuser;

--
-- Name: nnusers; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE nnusers (
    nnuser character varying
);


ALTER TABLE public.nnusers OWNER TO nnuser;

--
-- Name: parameter; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE parameter (
    param character varying,
    value character varying
);


ALTER TABLE public.parameter OWNER TO nnuser;

--
-- Name: perfdata; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfdata (
    host character varying NOT NULL,
    topic character varying NOT NULL,
    metric character varying NOT NULL,
    value numeric,
    daytime timestamp without time zone NOT NULL
);


ALTER TABLE public.perfdata OWNER TO nnuser;

--
-- Name: perfdata_0; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfdata_0 (CONSTRAINT perfdata_0_daytime_check CHECK ((((date(daytime) - '1970-01-01'::date) % 4) = 0))
)
INHERITS (perfdata);


ALTER TABLE public.perfdata_0 OWNER TO nnuser;

--
-- Name: perfdata_1; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfdata_1 (CONSTRAINT perfdata_1_daytime_check CHECK ((((date(daytime) - '1970-01-01'::date) % 4) = 1))
)
INHERITS (perfdata);


ALTER TABLE public.perfdata_1 OWNER TO nnuser;

--
-- Name: perfdata_2; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfdata_2 (CONSTRAINT perfdata_2_daytime_check CHECK ((((date(daytime) - '1970-01-01'::date) % 4) = 2))
)
INHERITS (perfdata);


ALTER TABLE public.perfdata_2 OWNER TO nnuser;

--
-- Name: perfdata_3; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfdata_3 (CONSTRAINT perfdata_3_daytime_check CHECK ((((date(daytime) - '1970-01-01'::date) % 4) = 3))
)
INHERITS (perfdata);


ALTER TABLE public.perfdata_3 OWNER TO nnuser;

--
-- Name: perfsum; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfsum (
    host character varying NOT NULL,
    date character varying NOT NULL,
    avgcpu numeric,
    avgcpu9_18 numeric,
    avgmem numeric
);


ALTER TABLE public.perfsum OWNER TO nnuser;

--
-- Name: perfsum2; Type: TABLE; Schema: public; Owner: nnuser; Tablespace:
--

CREATE TABLE perfsum2 (
    host character varying,
    date character varying,
    avgcpu numeric,
    avgcpu9_18 numeric,
    avgmem numeric
);


ALTER TABLE public.perfsum2 OWNER TO nnuser;

--
-- Name: hosts_pkey; Type: CONSTRAINT; Schema: public; Owner: nnuser; Tablespace:
--

ALTER TABLE ONLY hosts
    ADD CONSTRAINT hosts_pkey PRIMARY KEY (host);


--
-- Name: perfsum_pkey; Type: CONSTRAINT; Schema: public; Owner: nnuser; Tablespace:
--

ALTER TABLE ONLY perfsum
    ADD CONSTRAINT perfsum_pkey PRIMARY KEY (host, date);


--
-- Name: indx_perfdata_0_htmd; Type: INDEX; Schema: public; Owner: nnuser; Tablespace:
--

CREATE INDEX indx_perfdata_0_htmd ON perfdata_0 USING btree (host, topic, metric, daytime);


--
-- Name: indx_perfdata_1_htmd; Type: INDEX; Schema: public; Owner: nnuser; Tablespace:
--

CREATE INDEX indx_perfdata_1_htmd ON perfdata_1 USING btree (host, topic, metric, daytime);


--
-- Name: indx_perfdata_2_htmd; Type: INDEX; Schema: public; Owner: nnuser; Tablespace:
--

CREATE INDEX indx_perfdata_2_htmd ON perfdata_2 USING btree (host, topic, metric, daytime);


--
-- Name: indx_perfdata_3_htmd; Type: INDEX; Schema: public; Owner: nnuser; Tablespace:
--

CREATE INDEX indx_perfdata_3_htmd ON perfdata_3 USING btree (host, topic, metric, daytime);


--
-- Name: indx_perfdata_htmd; Type: INDEX; Schema: public; Owner: nnuser; Tablespace:
--

CREATE INDEX indx_perfdata_htmd ON perfdata USING btree (host, topic, metric, daytime);


--
-- Name: insert_perfdata_trigger; Type: TRIGGER; Schema: public; Owner: nnuser
--

CREATE TRIGGER insert_perfdata_trigger
    BEFORE INSERT ON perfdata
    FOR EACH ROW
    EXECUTE PROCEDURE perfdata_insert();


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- Name: hosts; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE hosts FROM PUBLIC;
REVOKE ALL ON TABLE hosts FROM nnuser;
GRANT ALL ON TABLE hosts TO nnuser;
GRANT SELECT ON TABLE hosts TO ronnuser;


--
-- Name: nnusers; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE nnusers FROM PUBLIC;
REVOKE ALL ON TABLE nnusers FROM nnuser;
GRANT ALL ON TABLE nnusers TO nnuser;
GRANT SELECT ON TABLE nnusers TO ronnuser;


--
-- Name: parameter; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE parameter FROM PUBLIC;
REVOKE ALL ON TABLE parameter FROM nnuser;
GRANT ALL ON TABLE parameter TO nnuser;
GRANT SELECT ON TABLE parameter TO ronnuser;


--
-- Name: perfdata; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfdata FROM PUBLIC;
REVOKE ALL ON TABLE perfdata FROM nnuser;
GRANT ALL ON TABLE perfdata TO nnuser;
GRANT SELECT ON TABLE perfdata TO ronnuser;


--
-- Name: perfdata_0; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfdata_0 FROM PUBLIC;
REVOKE ALL ON TABLE perfdata_0 FROM nnuser;
GRANT ALL ON TABLE perfdata_0 TO nnuser;
GRANT SELECT ON TABLE perfdata_0 TO ronnuser;


--
-- Name: perfdata_1; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfdata_1 FROM PUBLIC;
REVOKE ALL ON TABLE perfdata_1 FROM nnuser;
GRANT ALL ON TABLE perfdata_1 TO nnuser;
GRANT SELECT ON TABLE perfdata_1 TO ronnuser;


--
-- Name: perfdata_2; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfdata_2 FROM PUBLIC;
REVOKE ALL ON TABLE perfdata_2 FROM nnuser;
GRANT ALL ON TABLE perfdata_2 TO nnuser;
GRANT SELECT ON TABLE perfdata_2 TO ronnuser;


--
-- Name: perfdata_3; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfdata_3 FROM PUBLIC;
REVOKE ALL ON TABLE perfdata_3 FROM nnuser;
GRANT ALL ON TABLE perfdata_3 TO nnuser;
GRANT SELECT ON TABLE perfdata_3 TO ronnuser;


--
-- Name: perfsum; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfsum FROM PUBLIC;
REVOKE ALL ON TABLE perfsum FROM nnuser;
GRANT ALL ON TABLE perfsum TO nnuser;
GRANT SELECT ON TABLE perfsum TO ronnuser;


--
-- Name: perfsum2; Type: ACL; Schema: public; Owner: nnuser
--

REVOKE ALL ON TABLE perfsum2 FROM PUBLIC;
REVOKE ALL ON TABLE perfsum2 FROM nnuser;
GRANT ALL ON TABLE perfsum2 TO nnuser;
GRANT SELECT ON TABLE perfsum2 TO ronnuser;


--
-- PostgreSQL database dump complete
--
