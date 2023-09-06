import { useEffect } from "react";
import {FaPencilAlt } from "react-icons/fa";
import { useDispatch, useSelector } from "react-redux";
import Spinner from "../../../../../components/spinner/spinner";
import { GetInviteForCollaborations, collab_SliceActions } from "../../../../../store/collaborationSlice";
import { Actions } from "../invitecomponent/collabAction";
import { NoData } from "../../../../../components/nodata";
import { ViewportList } from "react-viewport-list";
import { useRef } from "react";

export const CollabContainer =()=>{
    const collab = useSelector(
        state => state.collab
    )
    const listRef = useRef(null)
    const dispatch = useDispatch();
    useEffect(()=>{
        dispatch(GetInviteForCollaborations(null));
    },[dispatch])

    const handleInputChange=(e)=>{
        dispatch(collab_SliceActions.searchdata({
            type:"collab",
            data:e.target.value
        }))
    }

    if(collab.GetInviteForCollaborationsStatus ==='pending'){
        return <Spinner/>
    }

    return(
        <>
        <Actions
            handleInputChange={handleInputChange}
        />
        <div className="w-overflow" ref={listRef}>
            <table className=" table table-striped table-hover table-bordered table-responsive caption-top mb-3">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Who has invited you for collaborations</th>
                        <th scope="col">Created At</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                <ViewportList viewportRef={listRef} items={collab.inviteForCollaborations} margin={8}>
                    {
                        (
                            (collaboration,index)=>{
                            const{
                                name,
                                createdAt
                            }= collaboration
                        
                            return(
                                <tr key={index}>
                                    <th scope="row" className="fs-6">{index+1}</th>
                                    <td className="fs-6">{name}</td>
                                    <td className="fs-6">{createdAt}</td>
                                    <td className="fs-6">
                                        <div className="d-flex align-items-center">
                                            <div className="d-flex align-items-center me-2 text-white bg bg-success rounded p-2">
                                                <span className="me-1">
                                                    <FaPencilAlt/>
                                                </span>
                                                <span>
                                                    Edit
                                                </span>
                                            </div>
                                            <div className="dropdown">
                                                <button
                                                    className="btn btn-secondary dropdown-toggle" 
                                                    type="button" 
                                                    data-bs-toggle="dropdown" 
                                                    aria-expanded="false" 
                                                >
                                                </button>
                                                <ul className="dropdown-menu">
                                                    <li
                                                        className="dropdown-item fs-6"
                                                    >
                                                        Visit Account
                                                    </li>
                                                    <li
                                                        className="dropdown-item fs-6"
                                                    >
                                                        Delete
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            )
                        })
                    }
                    </ViewportList>
                </tbody>
            </table>
        </div>
        {
            collab
            .inviteForCollaborations?.length === 0 && <NoData/>
        }
        </>
    )
}