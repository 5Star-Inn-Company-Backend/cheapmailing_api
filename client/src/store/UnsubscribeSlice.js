import { createAsyncThunk, createSlice} from '@reduxjs/toolkit';
import  axios  from 'axios';
import { toast } from 'react-toastify';
import { UpdateActivities } from './activitiesSlice';
import { apiBaseUrl, setHeaders } from './api';

export const DeleteUnSubscribers = createAsyncThunk(
    'unsubscriber/DeleteUnSubscribers', 
    async ({id},{dispatch}) =>{
    try{
        const response = await axios.delete(
            `${apiBaseUrl}/deleteunsubscribe/${id}`,
                setHeaders()
        )
        if(response?.data?.status){
            dispatch(GetUnSubscribers())
        }
        return response?.data
    } catch(err){
        toast.error(
            err.response?.data?.message
        )
        }
    }
)

export const GetUnSubscribers = createAsyncThunk(
    'unsubscriber/GetUnSubscribers', 
    async () =>{
    try{
        const response = await axios.get(
            `${apiBaseUrl}/unsubscribe`,
                setHeaders()
        )
        return response?.data
    } catch(err){
        return err.response?.data
        }
    }
)

export const CreateUnsubscriber = createAsyncThunk(
    'unsubscriber/CreateUnsubscriber', 
    async ({
        email,
        fname,
        lname,
        country,
        state,
        phone,
        dob,
        tag
    },{dispatch}) =>{
    try{
        const response = await axios.post(
            `${apiBaseUrl}/addunsubscribe`,{
                email,
                fname,
                lname,
                country,
                state,
                phone,
                dob,
                tag
            },
            setHeaders()
        )
        if(response?.data?.status){
            dispatch(UpdateActivities({
                action:`You added "${email}" to your Unsubscribed list`
            }));
            dispatch(GetUnSubscribers())
        }
        return response?.data
    } catch(err){
            toast.error(
                err.response?.data?.message
            )
        }
    }
)


const unsubscriber_Slice = createSlice({
    name:"unsubscriber",
    initialState: {
        unsubscribers:[],
        deleteStatus:'',
        unsubscribersToFilter:[],
        CreateUnsubscriberStatus:'',
        CreateUnsubscriberError:'',
        GetUnSubscribersStatus:'',
        GetUnSubscribersError:''
    },
    reducers:{
        sortDataByEmail(state,action){
            const newArray=[...state.unsubscribers]
            const sortByEmail =  newArray.sort((a, b)=> (a.email < b.email ) ? -1 : (a.email > b.email) ? 1 : 0);
            return{
                ...state,
                unsubscribers:sortByEmail
            }    
        },
        sortDataByName(state,action){
            const newArray=[...state.unsubscribers]
            const sortByName =  newArray.sort((a, b)=> (a.fname < b.fname) ? -1 : (a.fname > b.fname) ? 1 : 0);
            return{
                ...state,
                unsubscribers:sortByName
            }        
        },
        searchdata(state,action){
            const data=action.payload;
            const filteredData = state.unsubscribersToFilter.filter((item)=>item.fname.toLowerCase().includes(data.toLowerCase()));
            return{
                ...state,
                unsubscribers:filteredData
            }
        }
    },

    extraReducers:(builder)=>{

        builder.addCase(DeleteUnSubscribers.pending,(state, action)=>{
            return {
                ...state,
                deleteStatus:'pending'
            }

        });
        builder.addCase(DeleteUnSubscribers.fulfilled,(state, action)=>{
            if(action.payload){
                const{
                    message,
                    status
                }=action.payload
                if(status){
                    toast(message)
                    return{
                        ...state,
                        deleteStatus:"success"
                    }
                }else{
                    toast.error(message)
                    return{
                        ...state,
                        deleteStatus:"failed"
                    }
                }
            }else return{
                ...state,
                deleteStatus:"failed"
            }
        })
        builder.addCase(DeleteUnSubscribers.rejected,(state, action)=>{
            toast.error(action?.payload?.message)
            return{
                ...state,
                deleteStatus:'rejected'
            }
        })

        builder.addCase(GetUnSubscribers.pending,(state, action)=>{
            return {
                ...state,
                GetUnSubscribersStatus:'pending'
            }

        });
        builder.addCase(GetUnSubscribers.fulfilled,(state, action)=>{
            if(action.payload){
                const{
                    status,
                    message
                }=action.payload
                if(status){
                    return{
                        ...state,
                        unsubscribers: message,
                        unsubscribersToFilter:message,
                        GetUnSubscribersStatus:"success"
                    }
                }return{
                    ...state,
                    GetUnSubscribersStatus:"success"
                }
            }else return{
                ...state,
                GetUnSubscribersStatus:"failed"
            }
        })
        builder.addCase(GetUnSubscribers.rejected,(state, action)=>{
            return{
                ...state,
                CreateUnsubscriberStatus:'rejected'
            }
        })

        builder.addCase(CreateUnsubscriber.pending,(state, action)=>{
            return {
                ...state,
                CreateUnsubscriberStatus:'pending'
            }

        });
        builder.addCase(CreateUnsubscriber.fulfilled,(state, action)=>{
            if(action.payload){
                const {
                    message,
                    status
                }= action.payload
                if(status){
                    toast(message);
                    return{
                        ...state,
                        CreateUnsubscriberStatus:"success"
                    }
                }
                else{
                    toast.error(message);
                    return{
                    ...state,
                    CreateUnsubscriberStatus:"failed"
                    }
                }
            }else return{
                ...state,
                CreateUnsubscriberStatus:"failed"
            }
        })
        builder.addCase(CreateUnsubscriber.rejected,(state, action)=>{
            toast.error(action?.payload?.message)
            return{
                ...state,
                CreateUnsubscriberStatus:'rejected'
            }
        })
    }
})

export const unsubscriber_SliceActions = unsubscriber_Slice.actions;
export default unsubscriber_Slice;