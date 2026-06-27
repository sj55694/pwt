create table Webnovels
(
    id      integer not null
        constraint Webnovels_pk
            primary key autoincrement,
    Tytul text not null,
    Autor text not null,
    Opis text not null
);